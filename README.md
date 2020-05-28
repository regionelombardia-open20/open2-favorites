# amos-favorites

Extension for add a content, like news, events, etc..., in the favorites.

## Installation

### 1. Add module to your application

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
composer require open20/amos-favorites
```

or add this row

```
"open20/amos-favorites": "dev-master"
```

to the require section of your `composer.json` file.

### 2. Add module configuration

Add module to your main config in backend like this:
	
```php
<?php
'modules' => [
    'favorites' => [
        'class' => 'open20\amos\favorites\AmosFavorites',
        'modelsEnabled' => [
            /**
             * Add here the classnames of the models where you want the comments
             * (i.e. 'open20\amos\news\models\News')
             */
        ]
    ],
],
```

### 3. Apply migrations

To apply migrations you can launch this command:

```bash
php yii migrate/up --migrationPath=@vendor/open20/amos-favorites/src/migrations
```

or add this row to your migrations config in console:

```php
<?php
return [
    '@vendor/open20/amos-favorites/src/migrations',
];
```
