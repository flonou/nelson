Collection of commands to synchronize Github repositories with Crowdin translations

[![Build Status](https://travis-ci.org/akeneo/nelson.svg?branch=master)](https://travis-ci.org/akeneo/nelson)


## How does it work?

<img align="right" src="nelson.png" width="250"/>

Synchronizing a Github repository with Crowdin translations can be performed in a few steps.

First, you must tell Crowdin which keys to translate. In order to do that, you have to select the "source" locale from which your contributors will make their translations.
All translation keys are located in different files that you can configure using a Symfony Finder.
This tool has a command (`push-translation-keys`) to send new translations to Crowdin from the source locale.

Once the keys have been sent to Crowdin, your contributors will have the ability to edit, add and delete new translations in their language.

When the keys have been translated, you must pull them back in your Github repository. You can set an option in this tool to only pull languages that are translated to at least a certain percentage (e.g. 90%).
This command (`pull-translations`) tells Crowdin to create a package for each language, downloads the packages and checks the differences with your up-to-date repository.
If it detects differences (new translations, deleted translations or modified translations), it automatically creates a new Pull Request on your repository.
Finally, you have the option to manually accept or not the Pull Request depending on its content. This command can be automated via a Cron for daily translations.

And voilà! Your GitHub repository will always be up-to-date and users will have the latest translations.

## Installation

**Warning** You must create your own fork in a dedicated git repository that will only be used for this purpose. This will allow you to pull translations and create pull requests.
You must **never** run the following commands using your main repository, because nestor merges latests commits automatically.

```
  $ git clone git@github.com:akeneo/nelson.git
  $ cd nelson
  $ composer update
```

# Create your own configuration

This package includes a configuration example located in `app/config.example.yml` that you must adapt according to your settings and save it as `app/config.yml`.

If you want to manage several configurations for multiple projects, you can use `--config_file=yourconfig.yml`.

## How to use it?

- To display languages that are going to be synchronized

  `$ php app/console nelson:info-translated-progress`

- To create language packages in Crowdin

  `$ php app/console nelson:refresh-packages`

- To push the new translation keys to Crowdin

  `$ php app/console nelson:push-translation-keys`

- To pull translations (creates PR to your main repository)

  `$ php app/console nelson:pull-translations`


## Update the crontab

You can set up a crontab to run nelson processes on a regular basis.
The first step is always to refresh the Crowdin package to get latest updates.
To update crontab, use `crontab -e`.

A crontab example that pushes new keys every friday and creates pull requests every saturday:
```
30 * * * * cd /path/to/nelson/ && app/console nelson:refresh-packages > /tmp/nelson_refresh.log 2>&1
0 4 * * 1 cd /path/to/nelson/ && app/console nelson:pull-translations > /tmp/nelson_pull.log 2>&1
0 5 * * 1 cd /path/to/nelson/ && app/console nelson:push-translation-keys > /tmp/nelson_push.log 2>&1
```
