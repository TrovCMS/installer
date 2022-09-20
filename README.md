[![Run tests](https://github.com/trovcms/installer/workflows/Run%20tests/badge.svg?branch=main)](https://github.com/trovcms/installer/actions?query=workflow%3A%22Run+Tests%22)

**Quickly spin up a new TrovCMS application.**

[TrovCMS](https://github.com/TrovCMS/trov) is a starter kit for websites, built on [Filament](https://filamentphp.com) and [Laravel](https://laravel.com).

# Requirements

- PHP 8.0+
- (optional, but beneficial) [Laravel Valet](https://laravel.com/docs/valet)

# Installation

```bash
composer global require trovcms/installer
```

# Upgrading

```bash
composer global update trovcms/installer
```

# Usage

Make sure `~/.composer/vendor/bin` is in your terminal's path.

```bash
cd ~/<code-directory>
trov new my-cool-trov-app
```

# What exactly does it do?

- `trov new $PROJECTNAME`
- Initialize a git repo, add all the files, and, after some changes below, make a commit with the text "Initial commit."
- Replace the `.env` (and `.env.example`) database credentials with the default macOS MySQL credentials: database of `$PROJECTNAME`, user `root`, and empty password
- Replace the `.env` (and `.env.example`) `APP_URL` with `$PROJECTNAME.$YOURVALETTLD`
- Generate an app key
- Open the project in your favorite editor
- Open `$PROJECTNAME.$YOURVALETTLD` in your browser

> Note: If your `$PROJECTNAME` has dashes (`-`) in it, they will be replaced with underscores (`_`) in the database name.

There are also a few optional behaviors based on the parameters you pass (or define in your config file), including creating a database, migrating, running Valet Link and/or Secure, and running a custom bash script of your definition after the fact.

# Customizing Trov Installer

While the default actions Trov Installer provides are great, most users will want to customize at least a few of the steps. Thankfully, Trov Installer is built to be customized!

There are three ways to customize your usage of Trov Installer: command-line arguments, a config file, and an "after" file.

Most users will want to set their preferred configuration options once and then never think about it again. That's best solved by creating a config file.

But if you find yourself needing to change the way you interact with Trov Installer on a project-by-project basis, you may also want to use the command-line parameters to customize Trov Installer when you're using it.

## Creating a config file

You can create a config file at `~/.trov/config` rather than pass the same arguments each time you create a new project.

The following command creates the file, if it doesn't exist, and edits it:

```bash
trov edit-config
```

The config file contains the configuration parameters you can customize, and will be read on every usage of Trov Installer.

## Creating an "after" file

You can also create an after file at `~/.trov/after` to run additional commands after you create a new project.

The following command creates the file, if it doesn't exist, and edits it:

```bash
trov edit-after
```

The after file is interpreted as a bash script, so you can include any commands here, such as installing additional composer dependencies...

```bash
# Install additional composer dependencies as you would from the command line.
echo "Installing Composer Dependencies"
composer require awcodes/filament-quick-create spatie/laravel-ray
```

...or copying additional files to your new project.

```bash
# To copy standard files to new TrovCMS project place them in ~/.trov/includes directory.
echo "Copying Include Files"
cp -R ~/.trov/includes/ $PROJECTPATH
```

You also have access to variables from your config file such as `$PROJECTPATH` and `$CODEEDITOR`.

## Using command-line parameters

Any command-line parameters passed in will override Trov Installer's defaults and your config settings. See a [full list of the parameters you can pass in](#parameters).

# Trov Installer Commands

- `help` or `help-screen` show the help screen

<a id="config-files"></a>
- `edit-config` edits your config file (and creates one if it doesn't exist)

  ```bash
  trov edit-config
  ```

- `edit-after` edits your "after" file (and creates one if it doesn't exist)

  ```bash
  trov edit-after
  ```


<a id="parameters"></a>
# Configurable parameters

You can optionally pass one or more of these parameters every time you use Trov Installer. If you find yourself wanting to configure any of these settings every time you run Trov Installer, that's a perfect use for the [config files](#config-files).

- `-e` or `--editor` to define your editor command. Whatever is passed here will be run as `$EDITOR .` after creating the project.

  ```bash
  # runs "subl ." in the project directory after creating the project
  trov new my-cool-trov-app --editor=subl
  ```

- `-p` or `--path` to specify where to install the application.

  ```bash
  trov new my-cool-trov-app --path=~/Sites
  ```

- `-m` or `--message` to set the first Git commit message.

  ```bash
  trov new my-cool-trov-app --message="This trov runs fast!"
  ```

- `-f` or `--force` to force install even if the directory already exists

  ```bash
  # Creates a new Laravel application after deleting ~/Sites/my-cool-trov-app
  trov new my-cool-trov-app --force
  ```

- `-d` or `--dev` to choose the `develop` branch instead of `master`, getting the beta install.

  ```bash
  trov new my-cool-trov-app --dev
  ```

- `-b` or `--browser` to define which browser you want to open the project in.

  ```bash
  trov new my-cool-trov-app --browser="/Applications/Google Chrome Canary.app"
  ```

- `-l` or `--link` to create a Valet link to the project directory.

  ```bash
  trov new my-cool-trov-app --link
  ```

- `-s` or `--secure` to secure the Valet site using https.

  ```bash
  trov new my-cool-trov-app --secure
  ```

- `--create-db` to create a new MySQL database which has the same name as your project.
  This requires `mysql` command to be available on your system.

  ```bash
  trov new my-cool-trov-app --create-db
  ```

- `--migrate-db` to migrate your database.

  ```bash
  trov new my-cool-trov-app --migrate-db
  ```

- `--dbuser` to specify the database username.

  ```bash
  trov new my-cool-trov-app --dbuser=USER
  ```

- `--dbpassword` specify the database password.

  ```bash
  trov new my-cool-trov-app --dbpassword=SECRET
  ```

- `--dbhost` specify the database host.

  ```bash
  trov new my-cool-trov-app --dbhost=127.0.0.1
  ```

- `--full` to use `--create-db`, `--migrate-db`, `--link`, and `-secure`.

  ```bash
  trov new my-cool-trov-app --full

**GitHub Repository Creation**

**Important:** To create new repositories Trov Installer requires one of the following tools to be installed:
- the official [GitHub command line tool](https://github.com/cli/cli#installation)
- the [hub command line tool](https://github.com/github/hub#installation)

Trov Installer will give you the option to continue without GitHub repository creation if neither tool is installed.

- `-g` or `--github` to  Initialize a new private GitHub repository and push your new project to it.

```bash
# Repository created at https://github.com/<your_github_username>/my-cool-trov-app
trov new my-cool-trov-app --github
```

- Use `--gh-public` with `--github` to make the new GitHub repository public.

```bash
trov new my-cool-trov-app --github --gh-public
```

- Use `--gh-description` with `--github` to initialize the new GitHub repository with a description.

```bash
Trov Installer new my-cool-trov-app --github --gh-description='My cool TrovCMS application'
```
- Use `--gh-homepage` with `--github` to initialize the new GitHub repository with a homepage url.

```bash
Trov Installer new my-cool-trov-app --github --gh-homepage=https://example.com
```
- Use `--gh-org` with `--github` to initialize the new GitHub repository with a specified organization.

```bash
# Repository created at https://github.com/acme/my-cool-trov-app
trov new my-cool-trov-app --github --gh-org=acme
```

## License

Trov Installer is open-sourced software licensed under the [MIT license](LICENSE.md).
