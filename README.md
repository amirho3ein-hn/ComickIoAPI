
# Comick.io API PHP Class

This is a PHP class for interacting with the **Comick.io** website's API. It provides a variety of methods for retrieving comic data such as comic titles, chapters, cover images, and more. The class allows you to easily fetch data related to specific comics from the Comick.io platform, download chapters, and manage comic-related content.

## Table of Contents

1. [Installation](#installation)
2. [Requirements](#requirements)
3. [Methods](#methods)
    - [getComicData](#getcomicdata)
    - [getComicId](#getcomicid)
    - [getComicCountry](#getcomiccountry)
    - [getComicLastChapter](#getcomiclasterchapter)
    - [getComicCoverLink](#getcomiccoverlink)
    - [getComicSlug](#getcomicslug)
    - [getComicTitle](#getcomictitle)
    - [getComicChapters](#getcomicchapters)
    - [getComicChapter](#getcomicchapter)
    - [downloadComicChapter](#downloadcomicchapter)
4. [Usage Example](#usage-example)
5. [Contributing](#contributing)
6. [License](#license)

## Installation

To use this class, you need to include it in your PHP project. You can either download the `ComickIo.php` file or clone this repository into your project.

```bash
git clone https://github.com/amirho3ein-hn/ComickIoAPI.git
```

``` php
composer require amirho3ein-hn/ComickIoAPI
```

## Requirements

- PHP 7.4 or higher
- cURL enabled in PHP

## Methods

### `getComicData(string $comicUrl = "https://comick.io/comic/example-comic")`

This method returns the entire comic information by fetching the data from the provided comic URL.

#### Example:
```php
$comic = new ComickIo();
$data = $comic->getComicData("https://comick.io/comic/example-comic");
echo json_encode($data);
```

### `getComicId(string $comicUrl = null)`

This method returns the ID of the comic by fetching data from the provided comic URL.

#### Example:
```php
$comic = new ComickIo();
$data = $comic->getComicId("https://comick.io/comic/example-comic");
echo json_encode($data);
```

### `getComicCountry(string $comicUrl = null)`

Fetches the country of origin for the comic.

#### Example:
```php
$comic = new ComickIo();
$data = $comic->getComicCountry("https://comick.io/comic/example-comic");
echo json_encode($data);
```

### `getComicLastChapter(string $comicUrl = null)`

Returns the last chapter number of the comic.

#### Example:
```php
$comic = new ComickIo();
$data = $comic->getComicLastChapter("https://comick.io/comic/example-comic");
echo json_encode($data);
```

### `getComicCoverLink(string $comicUrl = null)`

Returns the URL for the comic's cover image.

#### Example:
```php
$comic = new ComickIo();
$data = $comic->getComicCoverLink("https://comick.io/comic/example-comic");
echo json_encode($data);
```

### `getComicSlug(string $comicUrl = null)`

Gets the "slug" (unique identifier) of the comic.

#### Example:
```php
$comic = new ComickIo();
$data = $comic->getComicSlug("https://comick.io/comic/example-comic");
echo json_encode($data);
```

### `getComicTitle(string $comicUrl = null)`

Returns the title of the comic.

#### Example:
```php
$comic = new ComickIo();
$data = $comic->getComicTitle("https://comick.io/comic/example-comic");
echo json_encode($data);
```

### `getComicChapters(string $comicUrl = null, string $lang = null, int $chapter = null)`

Fetches the list of chapters for a comic, optionally filtered by language and chapter number.

#### Example:
```php
$comic = new ComickIo();
$data = $comic->getComicChapters("https://comick.io/comic/example-comic", "en");
print_r($chapters);
```

### `getComicChapter(string $comicUrl, string $hid, string $lang, int $chapter)`

Returns data for a specific comic chapter, including images and other details.

#### Example:
```php
$comic = new ComickIo();
$data = $comic->getComicChapter("https://comick.io/comic/example-comic", "hid123", "en", 1);
print_r($chapterData);
```

### `downloadComicChapter(string $comicUrl, string $hid, string $lang, int $chapter, string $outputDir = 'downloadChapter')`

Downloads the comic chapter and stores the images in a ZIP file.

#### Example:
```php
$comic = new ComickIo();
$data = $comic->downloadComicChapter("https://comick.io/comic/example-comic", "hid123", "en", 1);
print_r($download);
```

## Usage Example

Below is a full usage example showing how to use multiple methods of the class:

```php
// Include the class
require_once 'ComickIo.php';

// Create an instance of the class
$comic = new ComickIo();

// Fetch comic details
$comicUrl = "https://comick.io/comic/example-comic";

// Get comic ID
$comicId = $comic->getComicId($comicUrl)['comicid'];
echo "Comic ID: $comicId
";

// Get the country of the comic
$country = $comic->getComicCountry($comicUrl)['country'];
echo "Country: $country
";

// Get the last chapter
$lastChapter = $comic->getComicLastChapter($comicUrl)['last_chapter'];
echo "Last Chapter: $lastChapter
";

// Get comic cover image URL
$coverLink = $comic->getComicCoverLink($comicUrl)['cover'];
echo "Cover Link: $coverLink
";

// Get comic title
$title = $comic->getComicTitle($comicUrl)['title'];
echo "Title: $title
";

// Get list of chapters
$chapters = $comic->getComicChapters($comicUrl)['chapters'];
echo "Chapters: 
";
print_r($chapters);
```

## Contributing

Feel free to fork this repository, submit issues, and create pull requests. All contributions are welcome!

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
