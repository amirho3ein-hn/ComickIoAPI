<?php

namespace ComickIoAPI ;
use Exception;

header('Content-Type: application/json');

/**
 * ComickIoAPI: A class to interact with Comick.io API.
 */
class ComickIoAPI {
     
    /**
     * @var string $base_url The base URL of the comic website. This is used as the root URL for making API requests.
     */
    private $base_url = 'https://comick.io/';

    /**
     * @var string $sub_pictures_url The base URL used for fetching comic images. It is used to construct the full URL for each image based on its specific key.
     */
    private $sub_pictures_url = 'https://meo.comick.pictures/';

    /**
     * @var string $build_id The build identifier used to specify a version of the website for API requests. It is appended to the URL to form the complete endpoint.
     */
    private $build_id = '.b363e10d94e9f3a02b28050f4be6d00894946df3';

    /**
     * @var array|null $comic_data Stores the comic data fetched from the API. Initially set to null, it is populated with data when fetched.
     */
    private $comic_data = null;

    /**
     * @var array|null $chapter_data Stores the chapter data fetched from the API. Initially set to null, it is populated with data when fetched.
     */
    private $chapter_data = null;
    

    /**
     * Constructor
     */
    public function __construct() {
        // Initialize the build ID if necessary.
        // $this->build_id = $this->getBuildId()['buildId'];
    }

    /**
     * Fetches content from a given URL and returns it as a decoded JSON response.
     *
     * This method uses cURL to send a GET request to the provided URL and retrieves the response. 
     * It handles SSL verification, follows redirects, and manages timeout settings. Once the response 
     * is fetched, it trims any unnecessary backticks and attempts to decode the response as JSON. 
     * If successful, it returns the JSON data; otherwise, it returns an error message.
     *
     * @param string $url The URL to fetch content from.
     * @return array An associative array containing:
     * - 'status' (bool): Indicates whether the operation was successful.
     * - 'message' (string): A message providing further information in case of errors.
     * - 'data' (array): The decoded JSON data if successful, or null if the response is invalid.
     */
    public function fetchApiContent($url) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; PHP script)");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new Exception("cURL Error: " . curl_error($ch));
            }
            curl_close($ch);

            $clean_data = trim($data, '`');
            $json_data = json_decode($clean_data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if (isset($json_data['notFound']) && $json_data['notFound']) {
                    return ['status' => false, 'message' => "notFound"];
                }
                return ['status' => true, 'data' => $json_data];
            } else {
                throw new Exception("Invalid JSON format");
            }
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }

    }


    /**
     * Fetches the raw content of a web page from a given URL.
     *
     * This method uses cURL to send a GET request to the provided URL and retrieves the raw content 
     * of the page. It handles SSL verification, follows redirects, and manages timeout settings. 
     * The method returns the raw page content if the request is successful; otherwise, it returns false.
     *
     * @param string $url The URL of the page to fetch content from.
     * @return mixed The raw content of the page if successful, or false if an error occurs.
     */
    public function fetchPageContent($url) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; PHP script)");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new Exception("cURL Error: " . curl_error($ch));
            }
            curl_close($ch);

            return $data;
        } catch (Exception $e) {
            return false;
        }
    }
    

    /**
     * Retrieve data for a specific comic using its URL.
     *
     * This method fetches the details of a comic from the provided comic URL.
     * It validates the URL format and extracts the comic name for making an API request.
     * If successful, the comic data is stored and returned. Otherwise, an error message is provided.
     *
     * @param string $comicUrl The URL of the comic (default example: "https://comick.io/comic/na-honjaman-level-up-ragnarok").
     * @return array An associative array containing:
     * - 'status': (bool) True if the data is retrieved successfully, false otherwise.
     * - 'data': (array|null) The comic data if successful.
     * - 'message': (string) An error message if the retrieval fails.
     */
    public function getComicData(string $comicUrl = "https://comick.io/comic/na-honjaman-level-up-ragnarok") {
        $comic = explode('/',$comicUrl);

        if ($comic[3] != 'comic') {
            return ['status' => false, 'message' => "The link format is incorrect. For example: https://comick.io/comic/na-honjaman-level-up-ragnarok" ];
        }

        // $comic_name = strtolower($comic[4]);
        $comic_name = $comic[4];

        $response = $this->fetchApiContent($this->base_url . "/_next/data/$this->build_id/comic/$comic_name.json?slug=$comic_name");

        if ($response['status']) {
            $this->comic_data = ['status' => true, 'data' => $response['data']];
            return ['status' => true, 'data' => $response['data']];
        }

        return ['status' => false, 'message' => "Problem getting the database"];

    }


    /**
     * Retrieve the build ID from the main page of the website.
     *
     * This method fetches the HTML content of the base URL and extracts the build ID
     * using a regular expression. The build ID is required for API requests to work correctly.
     * If the build ID is not found, an error message is returned.
     *
     * @return array An associative array containing:
     * - 'status': (bool) True if the build ID is retrieved successfully, false otherwise.
     * - 'buildId': (string|null) The extracted build ID if successful.
     * - 'message': (string) An error message if the build ID retrieval fails.
     */
    public function getBuildId() {

        $data = $this->fetchPageContent($this->base_url);

        if ($data === false) {
            return ['status' => false, 'message' => "Build ID not found!"];
        }
       
        $pattern = '/"buildId":"([^"]+)"/';

        if (preg_match($pattern, $data, $matches)) {
            return ['status' => true, 'buildId' => $matches[1]];
        } else {
            return ['status' => false, 'message' => "Build ID not found!"];
        }
    }


    /**
     * Retrieves the comic ID based on the provided comic URL or the class's stored comic data.
     *
     * This method checks if the comic URL is provided. If not, it uses the stored comic data
     * within the class. If the comic URL is provided, it fetches the comic data from an external 
     * source using the getComicData method. Once the comic data is available, it extracts the 
     * comic ID and returns it in the response.
     *
     * @param string|null $comicUrl The URL of the comic (optional). If null, it uses the stored comic data.
     * @return array An associative array containing:
     * - 'status' (bool): Indicates whether the operation was successful.
     * - 'message' (string): A message providing further information, especially if no comic URL or data is provided.
     * - 'comicid' (string|null): The ID of the comic, if successfully retrieved.
     */
    public function getComicId(string $comicUrl = null) {

        if ($comicUrl == null) {
            if ($this->comic_data == null) {
                return ['status' => false, 'message' => "Comic data not received. Please send the comicUrl parameter"];
            }else{
                $comic_data = $this->comic_data;
            }
        }else{
            $comic_data = $this->getComicData($comicUrl);
        }
        if ($comic_data['status'] === false) {
            return ['status' => false, 'message' => $comic_data['message']];
        }
        return ['status' => true, 'comicid' => $comic_data['data']['pageProps']['comic']['id']];

    }


    /**
     * Retrieves the comic's country based on the provided comic URL or the class's stored comic data.
     *
     * This method checks if the comic URL is provided. If not, it uses the stored comic data
     * within the class. If the comic URL is provided, it fetches the comic data from an external 
     * source using the getComicData method. Once the comic data is available, it extracts the
     * country information and returns it in the response.
     *
     * @param string|null $comicUrl The URL of the comic (optional). If null, it uses the stored comic data.
     * @return array An associative array containing:
     * - 'status' (bool): Indicates whether the operation was successful.
     * - 'message' (string): A message providing further information, especially if no comic URL or data is provided.
     * - 'country' (string|null): The country of the comic, if successfully retrieved.
     */
    public function getComicCountry(string $comicUrl = null) {

        if ($comicUrl == null) {
            if ($this->comic_data == null) {
                return ['status' => false, 'message' => "Comic data not received. Please send the comicUrl parameter"];
            }else{
                $comic_data = $this->comic_data;
            }
        }else{
            $comic_data = $this->getComicData($comicUrl);
        }
        if ($comic_data['status'] === false) {
            return ['status' => false, 'message' => $comic_data['message']];
        }
        return ['status' => true, 'country' => $comic_data['data']['pageProps']['comic']['country']];

    }
    

    /**
     * Retrieves the last chapter of the comic based on the provided comic URL or the class's stored comic data.
     *
     * This method checks if the comic URL is provided. If not, it uses the stored comic data
     * within the class. If the comic URL is provided, it fetches the comic data from an external 
     * source using the getComicData method. Once the comic data is available, it extracts the
     * last chapter information and returns it in the response.
     *
     * @param string|null $comicUrl The URL of the comic (optional). If null, it uses the stored comic data.
     * @return array An associative array containing:
     * - 'status' (bool): Indicates whether the operation was successful.
     * - 'message' (string): A message providing further information, especially if no comic URL or data is provided.
     * - 'last_chapter' (int|null): The last chapter number of the comic, if successfully retrieved.
     */
    public function getComicLastChapter(string $comicUrl = null) {

        if ($comicUrl == null) {
            if ($this->comic_data == null) {
                return ['status' => false, 'message' => "Comic data not received. Please send the comicUrl parameter"];
            }else{
                $comic_data = $this->comic_data;
            }
        }else{
            $comic_data = $this->getComicData($comicUrl);
        }
        if ($comic_data['status'] === false) {
            return ['status' => false, 'message' => $comic_data['message']];
        }
        return ['status' => true, 'last_chapter' => intval($comic_data['data']['pageProps']['comic']['last_chapter'])];

    }


    /**
     * Retrieves the cover image link of the comic based on the provided comic URL or the class's stored comic data.
     *
     * This method checks if the comic URL is provided. If not, it uses the stored comic data
     * within the class. If the comic URL is provided, it fetches the comic data from an external 
     * source using the getComicData method. Once the comic data is available, it constructs and
     * returns the cover image link of the comic.
     *
     * @param string|null $comicUrl The URL of the comic (optional). If null, it uses the stored comic data.
     * @return array An associative array containing:
     * - 'status' (bool): Indicates whether the operation was successful.
     * - 'message' (string): A message providing further information, especially if no comic URL or data is provided.
     * - 'cover' (string|null): The URL of the comic's cover image, if successfully retrieved.
     */
    public function getComicCoverLink(string $comicUrl = null) {

        if ($comicUrl == null) {
            if ($this->comic_data == null) {
                return ['status' => false, 'message' => "Comic data not received. Please send the comicUrl parameter"];
            }else{
                $comic_data = $this->comic_data;
            }
        }else{
            $comic_data = $this->getComicData($comicUrl);
        }
        if ($comic_data['status'] === false) {
            return ['status' => false, 'message' => $comic_data['message']];
        }
        return ['status' => true, 'cover' => $this->sub_pictures_url . "{$comic_data['data']['pageProps']['comic']['md_covers'][0]["b2key"]}"];

    }

    /**
     * Retrieves the comic's slug based on the provided comic URL or the class's stored comic data.
     *
     * This method checks if the comic URL is provided. If not, it uses the stored comic data
     * within the class. If the comic URL is provided, it fetches the comic data from an external 
     * source using the getComicData method. Once the comic data is available, it extracts the
     * slug of the comic and returns it in the response.
     *
     * @param string|null $comicUrl The URL of the comic (optional). If null, it uses the stored comic data.
     * @return array An associative array containing:
     * - 'status' (bool): Indicates whether the operation was successful.
     * - 'message' (string): A message providing further information, especially if no comic URL or data is provided.
     * - 'slug' (string|null): The slug of the comic, if successfully retrieved.
     */
    public function getComicSlug(string $comicUrl = null) {

        if ($comicUrl == null) {
            if ($this->comic_data == null) {
                return ['status' => false, 'message' => "Comic data not received. Please send the comicUrl parameter"];
            }else{
                $comic_data = $this->comic_data;
            }
        }else{
            $comic_data = $this->getComicData($comicUrl);
        }
        if ($comic_data['status'] === false) {
            return ['status' => false, 'message' => $comic_data['message']];
        }
        return ['status' => true, 'slug' => $comic_data['data']['pageProps']['comic']['slug']];

    }


    /**
     * Retrieves the title of the comic based on the provided comic URL or the class's stored comic data.
     *
     * This method checks if the comic URL is provided. If not, it uses the stored comic data
     * within the class. If the comic URL is provided, it fetches the comic data from an external 
     * source using the getComicData method. Once the comic data is available, it extracts the
     * title of the comic and returns it in the response.
     *
     * @param string|null $comicUrl The URL of the comic (optional). If null, it uses the stored comic data.
     * @return array An associative array containing:
     * - 'status' (bool): Indicates whether the operation was successful.
     * - 'message' (string): A message providing further information, especially if no comic URL or data is provided.
     * - 'title' (string|null): The title of the comic, if successfully retrieved.
     */
    public function getComicTitle(string $comicUrl = null) {

        if ($comicUrl == null) {
            if ($this->comic_data == null) {
                return ['status' => false, 'message' => "Comic data not received. Please send the comicUrl parameter"];
            }else{
                $comic_data = $this->comic_data;
            }
        }else{
            $comic_data = $this->getComicData($comicUrl);
        }
        if ($comic_data['status'] === false) {
            return ['status' => false, 'message' => $comic_data['message']];
        }
        return ['status' => true, 'title' => $comic_data['data']['pageProps']['comic']['title']];

    }
    

    /**
     * Retrieves the chapters of a comic based on the provided comic URL, language, and chapter number.
     *
     * This method allows fetching the list of comic chapters based on the provided comic URL. If the
     * URL is not provided, it uses the stored comic data within the class. The method also allows filtering
     * chapters by language and a specific chapter number if provided. If the chapter number is not provided,
     * it returns all chapters. If a language is provided, only chapters in that language are returned.
     *
     * @param string|null $comicUrl The URL of the comic (optional). If null, it uses the stored comic data.
     * @param string|null $lang The language code for filtering chapters (optional). If null, chapters are not filtered by language.
     * @param int|null $chapter The specific chapter number to retrieve (optional). If null, all chapters are returned.
     * @return array An associative array containing:
     * - 'status' (bool): Indicates whether the operation was successful.
     * - 'message' (string): A message providing further information, especially if no chapters or specific chapters are available.
     * - 'chapters' (array): The list of chapters matching the criteria, if successfully retrieved.
     */
    public function getComicChapters(string $comicUrl = null,string $lang = null,int $chapter = null,$type="chapter") {

        if ($comicUrl == null) {
            if ($this->comic_data == null) {
                return ['status' => false, 'message' => "Comic data not received. Please send the comicUrl parameter"];
            }else{
                $comic_data = $this->comic_data;
            }
        }else{
            $comic_data = $this->getComicData($comicUrl);
        }
        if ($comic_data['status'] === false) {
            return ['status' => false, 'message' => $comic_data['message']];
        }
        
        if ($lang == null) {
            $data_1 = [];
            foreach ($comic_data['data']['pageProps']['firstChapters'] as $firstChapters) {
                $data_1[] = [
                    'id' => isset($firstChapters['id']) ? $firstChapters['id'] : null,
                    'hid' => isset($firstChapters['hid']) ? $firstChapters['hid'] : null,
                    'title' => isset($firstChapters['title']) ? $firstChapters['title'] : null,
                    'lang' => isset($firstChapters['lang']) ? $firstChapters['lang'] : null,
                    'vol' => isset($firstChapters['vol']) ? $firstChapters['vol'] : null,
                    'chap' => isset($firstChapters['chap']) ? $firstChapters['chap'] : 1,
                ];
            }
        }else{
            $arr = [];
            foreach ($comic_data['data']['pageProps']['firstChapters'] as $firstChapters) {
                if ($firstChapters['lang'] == $lang) {
                    $arr[] = [
                        'id' => isset($firstChapters['id']) ? $firstChapters['id'] : null,
                        'hid' => isset($firstChapters['hid']) ? $firstChapters['hid'] : null,
                        'title' => isset($firstChapters['title']) ? $firstChapters['title'] : null,
                        'lang' => isset($firstChapters['lang']) ? $firstChapters['lang'] : null,
                        'vol' => isset($firstChapters['vol']) ? $firstChapters['vol'] : null,
                        'chap' => isset($firstChapters['chap']) ? $firstChapters['chap'] : 1,
                    ];
                }
            }
            if (count($arr) > 0) {
                $data_1 = $arr;
            }else{
                return ['status' => false, 'message' => "We do not have Chapters in this language yet"];
            }
        }

    
        if ($chapter == null) {
            return ['status' => true, 'chapters' => $data_1];
        }else{
            $arr = [];
            foreach ($data_1 as $firstChapters) {
                $response = $this->fetchApiContent($this->base_url . "/_next/data/$this->build_id/comic/{$this->getComicSlug()['slug']}/{$firstChapters['hid']}-$type-{$firstChapters['chap']}-{$firstChapters['lang']}.json?slug={$this->getComicSlug()['slug']}&chapter={$firstChapters['hid']}-$type-{$firstChapters['chap']}-{$firstChapters['lang']}");
                if (isset($response['data']['pageProps']['__N_REDIRECT_STATUS']) && $response['data']['pageProps']['__N_REDIRECT_STATUS'] == 308) {
                    return ['status' => false, 'message' => "type is volume"];
                }

                foreach ($response['data']['pageProps']['chapters'] as $res) {
                    if ($chapter == $res['chap']) {
                        $arr[] = [
                            'id' => isset($res['id']) ? $res['id'] : null,
                            'hid' => isset($res['hid']) ? $res['hid'] : null,
                            'title' => isset($res['title']) ? $res['title'] : null,
                            'lang' => isset($res['lang']) ? $res['lang'] : null,
                            'vol' => isset($res['vol']) ? $res['vol'] : null,
                            'chap' => isset($res['chap']) ? $res['chap'] : 1,
                        ];
                        break;
                    }
                }

            }
            if (count($arr) > 0) {
                return ['status' => true, 'chapters' => $arr];
            }else{
                return ['status' => false, 'message' => "This part of this Chapters is not available yet"];
            }
        }

    }


    /**
     * Retrieves the images of a specific comic chapter based on the provided comic URL, chapter ID, language, and chapter number.
     *
     * This method fetches the data for a specific comic chapter, using the provided comic URL, chapter ID (hid), 
     * language, and chapter number. It then extracts the image URLs of the chapter and returns them in the response. 
     * If the data cannot be fetched, it returns an error message.
     *
     * @param string $comicUrl The URL of the comic. This is used to retrieve the slug for the comic.
     * @param string $hid The chapter ID (hidden ID) for the specific chapter.
     * @param string $lang The language code for the chapter.
     * @param int $chapter The chapter number.
     * @return array An associative array containing:
     * - 'status' (bool): Indicates whether the operation was successful.
     * - 'message' (string): A message providing further information, especially if there is an issue fetching the data.
     * - 'images' (array): The list of image URLs for the chapter if successfully retrieved.
     */
    public function getComicChapter(string $comicUrl,string $hid,string $lang,int $chapter) {

        $response = $this->fetchApiContent($this->base_url . "/_next/data/$this->build_id/comic/{$this->getComicSlug($comicUrl)['slug']}/$hid-chapter-$chapter-$lang.json?slug={$this->getComicSlug($comicUrl)['slug']}&chapter=$hid-chapter-$chapter-$lang");
        
        if ($response['status']) {
            $this->chapter_data = ['status' => true, 'data' => $response['data']];

            $arr = [];
            foreach ($response['data']['pageProps']['chapter']['md_images'] as $image) {
                $arr[] = [
                    'url' => $this->sub_pictures_url . $image['b2key'],
                ];
            }
            return ['status' => true, 'images' => $arr];
        }

        return ['status' => false, 'message' => "Problem getting the data"];

    }

    /**
     * Downloads the images of a specific comic chapter and creates a ZIP file containing them.
     *
     * This method fetches the data for a specific comic chapter, including its images, and creates a ZIP file 
     * with the downloaded images. If the ZIP file already exists, it returns the file's URL. If the ZIP file 
     * cannot be created or if an error occurs while downloading the images, it returns an error message.
     *
     * @param string $comicUrl The URL of the comic. This is used to retrieve the slug for the comic.
     * @param string $hid The chapter ID (hidden ID) for the specific chapter.
     * @param string $lang The language code for the chapter.
     * @param int $chapter The chapter number.
     * @param string $outputDir The directory where the ZIP file will be saved (optional). Defaults to 'downloadChapter'.
     * @return array An associative array containing:
     * - 'status' (bool): Indicates whether the operation was successful.
     * - 'message' (string): A message providing further information, especially if an error occurs.
     * - 'zipName' (string): The name of the created ZIP file.
     * - 'zipUrl' (string): The URL to access the created ZIP file.
     */
    public function downloadComicChapter(string $comicUrl, string $hid, string $lang, int $chapter, string $outputDir = 'downloadChapter') {

        $chapterData = $this->getComicChapter($comicUrl, $hid, $lang, $chapter);
        
        if (!$chapterData['status']) {
            return ['status' => false, 'message' => "Error fetching chapter data: " . $chapterData['message']];
        }
    
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
    
        $zipFileName = "{$this->getComicSlug($comicUrl)['slug']}-$hid-$lang-$chapter.zip";
        $zipFilePath = $outputDir . DIRECTORY_SEPARATOR . $zipFileName;
    
        if (file_exists($zipFilePath)) {
            $domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
            $rootPath = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR);
            $relativePath = str_replace($rootPath, '', realpath($zipFilePath));
            $fileUrl = $domain . "/" . ltrim(str_replace(DIRECTORY_SEPARATOR, "/", $relativePath), "/");

            return [
                'status' => true,
                'message' => "ZIP file already exists",
                'zipName' => $zipFileName,
                'zipUrl' => $fileUrl
            ];
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return ['status' => false, 'message' => "Failed to create ZIP file"];
        }
    
        foreach ($chapterData['images'] as $index => $imageData) {
            $imageUrl = $imageData['url'];
            $imageContent = @file_get_contents($imageUrl);
    
            if ($imageContent === false) {
                $zip->close();
                return ['status' => false, 'message' => "Failed to download image: $imageUrl"];
            }
    
            $imageName = "image-" . ($index + 1) . ".jpg";
            $zip->addFromString($imageName, $imageContent);
        }
    
        $zip->close();
    
        $domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    
        $rootPath = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR);
    
        $relativePath = str_replace($rootPath, '', realpath($zipFilePath));
    
        $fileUrl = $domain . "/" . ltrim(str_replace(DIRECTORY_SEPARATOR, "/", $relativePath), "/");
    
        return [
            'status' => true,
            'message' => "ZIP file created successfully",
            'zipName' => $zipFileName,
            'zipUrl' => $fileUrl
        ];
    }
 
}
