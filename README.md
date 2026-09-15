# Bluecadet Public Files Manager

A Drupal module that scans the public files directory (`public://`) and reports on files that exist on disk but aren't tracked in Drupal's managed file table, so unused/orphaned files can be identified and cleaned up.

## Requirements

- Drupal 10.5+ or Drupal 11.2+
- PHP 8.2 or higher

## Versions

### 2.x Branch

- **2.0.x**: Drupal 10.5+/11.2+ support (PHP 8.2+)

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

This module includes automated tests that run via GitHub Actions against Drupal 10.5.x-11.3.x (see `.github/drupal-ci.yml` for the exact PHP/MariaDB matrix).

### Test Plan

#### Automated Tests (GitHub Actions)

The CI pipeline runs the following for each Drupal version:

1. **PHPCS** - Drupal coding standards validation (`Drupal` and `DrupalPractice` standards)
2. **PHPStan** - static analysis for deprecated API usage
3. **PHPUnit** - automated tests

#### Current coverage

A functional test confirms the admin report page is access-controlled (403 for anonymous, 200 for a user with "access administration pages") and loads successfully. A Kernel test covers `hook_update_status_alter()`. No coverage yet of the scan/check queue workers or the reset form's data-clearing behavior.

## Changelog

### 2.0.x

- Added Drupal 11 compatibility (`drupal/core: ^10.5 || ^11.2`, PHP 8.2+); dropped Drupal 9 support. `composer.json` was previously missing a `drupal/core` constraint entirely.
- Adopted the reusable GitHub Actions workflow architecture; moved CI to a shared, config-driven orchestrator in `bluecadet/web-gh-actions`
- Fixed `DatabaseTrait`/`CheckPublicFiles`'s `$database` properties being `private` on classes that use Drupal's `DependencySerializationTrait` (can't `__sleep()`/`__wakeup()` a subclass's private property)
- Fixed several postcss plugins that were silently relying on an old transitive dependency rather than being declared directly; updated `@bluecadet/drops` to `^1.2.1`
- Fixed a copy-pasted test docblock/`@group` in `PublicFilesTest.php` left over from another project
- Added Kernel test coverage for `hook_update_status_alter()`

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
