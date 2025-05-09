# CONTENT GENERATOR Plugin

This plugin is a content generation tool using ChatGPT for WordPress.

## Installation and Update System Setup

### Method 1: Using a Personal Server

1. Upload the files in the `update-server` folder to a web server.
2. Place a zipped version of the plugin named `wp-gpt-intermediate-1.0.5.zip` in the `update-server/files` folder. (The name and version should match the update server settings)
3. In the plugin settings, "Update Settings" section, enter the update server address. Example: `https://example.com/update-server`

### Method 2: Using GitHub (Recommended)

1. Make sure your GitHub repository is public at: https://github.com/javidmirzaei/GPT-plugin
2. Create a new release in GitHub with a tag matching your plugin version (e.g., `v1.0.5`)
3. In the plugin settings, leave the "Update Server Address" field empty so the update system uses GitHub

## How to Create a GitHub Release

1. Go to your GitHub repository (https://github.com/javidmirzaei/GPT-plugin)
2. Click on "Releases" in the right sidebar
3. Click "Create a new release"
4. Set the tag version to match your plugin version (e.g., `v1.0.5`)
5. Add a title and description for your release
6. Upload a ZIP file of your plugin (optional but recommended)
7. Click "Publish release"

## Update Server File Structure (When Using a Personal Server)

1. `version.php`: This file returns the current version number of the plugin
2. `info.php`: This file returns complete information about the new version in JSON format
3. `download.php`: This file is used to download the ZIP file of the new version
4. `files/`: A folder for storing ZIP files of new versions

## How to Release a New Version

1. Increase the version number in the main plugin file (`wp-gpt-intermediate.php`)
2. If using GitHub: create a new release with the matching version tag
3. If using a personal server: update the `version.php` and `info.php` files and upload a new ZIP file

After completing these steps, users can update the plugin to the new version through their WordPress admin panel. 