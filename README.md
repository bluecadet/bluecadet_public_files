# Bluecadet Public Files Manager

A Drupal module that scans the public files directory (`public://`) and reports on files that exist on disk but aren't tracked in Drupal's managed file table, so unused/orphaned files can be identified and cleaned up.

## Requirements

- Drupal 9.4+ or Drupal 10.1+
- PHP 7.4 or higher

## Versions

### 2.x Branch

- **2.0.x**: Drupal 9.4+/10.1+ support (PHP 7.4+/8.0+)

<!-- Older/unsupported branches can get a one-line note instead, e.g.
     "### 1.x Branch — completely outdated, do not use." -->

## Includes

- Two-stage cron queue that walks `public://` (skipping `styles`, `js`, `css`, `ctools`, and `private`) and cross-references each file's URI against `file_managed` to find files with no matching managed file record
- Logs untracked files (URI, filename, filesize, timestamp) to a dedicated `bluecadet_public_files` DB table
- Admin report page at `/admin/config/media/public-files-report` showing total queued directories/files, total untracked filesize, a sortable/pageable table of untracked files, and a form to reset the scan and re-queue `public://` from scratch

## Not using Composer

If you are not using composer, you can delete all unneeded files.

- composer.json

## Using Composer

If you are using composer to manage Drupal modules, make sure you add custom
location for this module to be downloaded to. You must add the installer types
line as well as the location for the module.

```json
  ...
  "installer-types": ["custom-drupal-module"],
  "installer-paths": {
    "web/core": ["type:drupal-core"],
    "web/modules/contrib/{$name}": ["type:drupal-module"],
    "web/modules/custom/{$name}": ["type:custom-drupal-module"],
    "web/profiles/contrib/{$name}": ["type:drupal-profile"],
    "web/themes/contrib/{$name}": ["type:drupal-theme"],
    "drush/contrib/{$name}": ["type:drupal-drush"]
  },
  ...
```

## Testing

This module includes automated tests that run via GitHub Actions against Drupal 9.4.x-10.1.x (see `.github/workflows/drupal-tests-and-standards.yml` for the exact PHP/MariaDB matrix).

### Test Plan

#### Automated Tests (GitHub Actions)

The CI pipeline runs the following for each Drupal version:

1. **PHPCS** - Drupal coding standards validation
2. **DrupalPractice** - Best practices validation
3. **Drupal-Check** - Static analysis for deprecated/removed API usage
4. **PHPUnit** - automated tests

#### Current coverage

A single functional test confirms the admin report page is access-controlled (403 for anonymous, 200 for a user with "access administration pages") and loads successfully. No coverage yet of the scan/check queue workers or the reset form's data-clearing behavior.

## Changelog

### 2.0.0

- Scans your public files directory and logs all files.

### 1.0.0

- Scans your public files directory and logs all files.

<br>
<br>
<br>

## Proudly developed @ Bluecadet

<p style="background-color: white; padding: 20px">
  <a href="https://www.bluecadet.com/"><img style="max-width: 50%; min-width: 300px; background: white; padding: 20px;" src="https://www.bluecadet.com/wp-content/themes/bluecadet-2018/images/logo/logo-bluecadet-black.svg" alt="Bluecadet"></a>
</p>
