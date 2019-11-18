# Baidu Aip picture recognition

> Give the Aip namespace in PHP 7.0+ 

## Features

- PHP7.0 + 
- Namespace
- Package Management By Composer

## Installation

- `composer require linij/baiduaip`

## Usage

```php
 $ocr = new AipOcr($appId, $apiKey, $secretKey);
 $ocr->receipt(file_get_contents($imagePath));
```
