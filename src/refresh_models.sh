#!/bin/bash

php application ide-helper:model --reset -W App\\Models\\Article
php application ide-helper:model --reset -W App\\Models\\ArticleLocalization
php application ide-helper:model --reset -W App\\Models\\Category
php application ide-helper:model --reset -W App\\Models\\CategoryLocalization
php application ide-helper:model --reset -W App\\Models\\Paragraph
php application ide-helper:model --reset -W App\\Models\\ParagraphTranslation
php application ide-helper:model --reset -W App\\Models\\Image
php application ide-helper:model --reset -W App\\Models\\Locale
php application ide-helper:model --reset -W App\\Models\\User
php application ide-helper:model --reset -W App\\Models\\Tag
php application ide-helper:model --reset -W App\\Models\\TagLocalization

