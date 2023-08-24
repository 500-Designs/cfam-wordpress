# Lucera Bootstrap WP Theme

## Overview

This is a custom starter WP theme using the [Timber](https://www.upstatement.com/timber/) (Twig Template Engine) to create object-oriented code and [ACF Pro](https://www.advancedcustomfields.com/pro/) to create the database structure.

The front end makes use of [PostCSS](https://postcss.org/). Assets are bundled using Webpack.

The site is hosted on [WP Engine](https://wpengine.com). Deploys are managed directly by git when pushing new commits. This means, some files are required to be in the repository such as compiled assets, dependencies, and plugins.

## Prerequisites

-   [ ] Lucera-owned repository (Lucera developers to set up and request the appropriate permissions)
-   [ ] WP Engine environment (Eduardo Cacas to set up)
-   [ ] Install ACF plugin (Lucera developers to install and add key)
-   [ ] Install Gravity Forms if needed (Lucera developers to install and add key)

## Development

This repository is for reference only, and should not be edited directly for projects. When setting up a new site, follow the [standard procedure for creating a repository](https://wpengine.com/support/git/#Copy_Content_Locally). Download this repository as a zip file, then unzip, place this folder into the `wp-content/themes` directory of your project repository, then customize to your needs.

### Setting up your local

-   You will need to configure your `wp-config.php` file to connect to your local database, set the home URL to match your local, and enable debugging. Add the following lines before the `That's all, stop editing!` comment:

```
define( 'WP_DEBUG', true );
define( 'WP_HOME', 'http://YOUR-LOCAL-DOMAIN' );
define( 'WP_SITEURL', 'http://YOUR-LOCAL-DOMAIN' );
```

-   Run `git remote add wpe-dev git@git.wpengine.com:production/ENDPOINT.git` to add WPEngine as a remote endpoint. Run `git remote -v` to verify your endpoints are set up correctly. It should look something like this:

```
origin	git@github.com:luceracloud/PROJECTNAME.git (fetch)
origin	git@github.com:luceracloud/PROJECTNAME.git (push)
wpe-dev	git@git.wpengine.com:production/ENDPOINT.git (fetch)
wpe-dev	git@git.wpengine.com:production/ENDPOINT.git (push)
```

### Plugin expectations

Plugin files are kept in the repository, and should be installed/updated locally and pushed to the server. Be judicious about installing plugins--only install if it's something you cannot accomplish using PHP and/or Javascript. For example, tracking scripts and UI components (e.g. carousels) should be done in code, rather than via plugins. By reducing our plugin usage, we reduce our site's overhead and keep more of the code directly within our control.

Here are some plugins we often use:

-   ACF to REST API
-   Fakerpress (for development only)
-   Gravity Forms
-   Redirection
-   WMPL
-   Yoast SEO
-   Yoast Duplicate Post

### Quality expectations

All pages/features/etc should be tested for the following dimensions. When you are ready to begin testing, you can ask Eduardo Cacas to create a testing matrix document for you to use.

#### Cross-browser

-   [ ] Responsive (test locally and resize the browser to be sure the site is presentable for all screen sizes)
-   [ ] Chrome
-   [ ] Safari
-   [ ] Microsoft Edge
-   [ ] IE11
-   [ ] iOS
-   [ ] Android

#### Accessibility

The goal is to receive a 100 on the Lighthouse audit for all pages. See the `A11Y.md` file contained in this project for more information.

-   [ ] Lighthouse accessibility audit, desktop
-   [ ] Lighthouse accessibility audit, mobile
-   [ ] Manual VoiceOver testing (Safari, MacOS)
-   [ ] Manual keyboard testing

#### Performance

-   [ ] Lighthouse performance audit, desktop
-   [ ] Lighthouse performance audit, mobile

### Environment branches & workflow

For new builds, all changes can be done on the `main` branch and deployed to the `wpe-dev` environment.

For existing builds, we are using branches named after their corresponding environments:

-   `dev` branch => deploys to `wpe-dev/dev`
-   `staging` branch => deploys to `wpe-dev/staging`
-   `main` branch => deploys to `wpe-prod/main` (Only contains code that is approved and ready for launch.)

When adding a new feature, create a new branch off `master` and include the Clubhouse ticket number in the name (e.g. `phase-2/cd-default-template-11504`, `feature/enable-revisions-11400`, `bugs/wpml-11355`). Merge your feature branch into the `dev` branch to test it on the server and to send off for review/approval. _Only_ merge your branch into `master` if it is approved and ready for launch.

### Setting up the theme

When working for the theme for the first time, `cd` into the theme root in your project. Vendor files are tracked in the repository, so you'll only need to run `npm install` to install dependencies.

### Local Development

All front end files (templates and assets) can be found in the `resources` directory. Template files live in `resources/views` and are organized structurally. Source assets (CSS, JS, images) live in `resources/assets`, and are compiled into the `dist` directory in the theme root.

All build processes should be run from the theme root. To watch files and recompile on change, run `npm run watch`. To build files for production, run `npm run build`.

### Templating: retrieving and manipulating data

The [Timber ACF Cookbook](https://timber.github.io/docs/guides/acf-cookbook/) is a great resource for retrieving data from your ACF fields. You can also use the `{{ dump() }}` function to get the entire object, to help you find the data you need. For example, for a featured image: `{{ dump(post.thumbnail) }}`.

Twig also comes with a number of tags, filters, and functions to let you retrieve and manipulate your data as needed. [Read the Twig documentation for more.](https://twig.symfony.com/doc/3.x/)

### Creating a custom Gutenberg block

1. Register your block inside a custom PHP file and assign a template.

Create a directory inside `resources/views/blocks` and name it after your block. Inside the directory, create a PHP and a Twig file with the same name. Inside the PHP file, you'll register your block and assign the template file. See the `example` directory for a reference.

2. Add the block to the list of ACF registered blocks.

Inside `app/plugins/acf/index.php`, look for the `$registered_blocks` array starting at line 40. Add the name of your block on a new line. This should match the registered name of your block inside your PHP (see line 8 in `resources/views/blocks/example/example.php`). By adding this line, you're telling the ACF plugin that your custom block exists, so you can begin assigning fields to it.

3. Add your fields inside the CMS.

Log in to your _local_ Wordpress install (_not_ on the server). Go to Custom Fields > Field Groups > Add New. Name your field group "Block: [Block-Name-Here]", add your fields, and under Location Rules, configure it to show the field group if Block is equal to [Your-Custom-Block].

Hit "Publish". This will generate a JSON object inside `app/plugins/acf/fields`. Commit and push this object to the server. When pushing (or pulling) JSON objects, you'll notice that a "Sync Available" link becomes available on the "Field Groups" page inside Wordpress. This lets us automatically sync field changes across multiple environments.

4. Write your markup.

So by now, you've created a custom block, assigned a template file, registered it with ACF, added your custom fields, and (presumably) added some data. Now it's time to write some markdown! Go into the template file you created in step 1 and dig in. Again, here, you can use the "example" block as a reference.

### Adding a new page template

1. Create your PHP and Twig files.

Similar to step 1 of creating a custom Gutenberg block, you will need to create a set of complementary PHP and Twig files for your new template, inside `resources/views/templates`. Logic lives in the PHP; markup lives in Twig. Look to `front-page` or `general` for a reference.

2. Manually set the template in the app.

Template files are manually managed as an array beginning on line 185 (`public function set_templates()`) in `app/lucera.php`. Here, name your template file and link it to your PHP. Be sure to keep the template names in alphabetical order. This makes the template file available for both Wordpress and ACF. From here, you can go to ACF and configure your fields.

3. Route the template file accordingly.

In order to make sure the user actually reaches your new page, go to `index.php` in the theme root and add a conditional in the appropriate section (`switch (get_page_template_slug())`).

### Other cool stuff

#### Custom utilities

In lieu of a CSS framework, we're adding our own custom utility classes. Find them all in `resources/assets/styles/utilities/`.

You can use these classes by adding them directly to your markup, or by using `@extend .utility-class` in your CSS to apply those styles wherever you need.

#### SVG icons

We're using an SVG macro make it a breeze to add inline SVGs. Most of the icons you'll need are already in `resources/views/macros/svg.twg` and are organized alphabetically. To add an SVG to your markup, add this to the top of your template file:

```
{% import 'resources/views/macros/svg.twig' as svg %}
```

Then wherever you'd like to add the SVG:

```
{{ svg.download('custom-class-1 custom-class-2','Here is a custom title') }}
```

The first part ('svg') calls the macro. The second ('download') specifies which SVG you'd like to use. As you can see in the example above, you can pass in any custom classes you like, as well a custom title. SVG titles are useful for accessibility, so if you do not want the icon to be decorative, remember to include a value.

SVGs have a `fill: currentColor` style applied by default, so to color the SVG, simply add a color class (e.g. `text-red`).

#### Lazy loading images

To lazy load images, set up your image tags like this:

```
<img
	alt="Maybe add a value, or leave the attribute empty if the image is decorative."
	class="lazy"
	data-src="/path/to/the/original/image.jpg"
	src="/path/to/the/low-res/image-50x30.jpg"
/>
```

You can use the Twig `resize` function to create the low-res version (it doesn't have to be 50x30--I usually go for 10% of the original object size). That's it!

## Deployment

Push to the `main` branch on the WP Engine endpoint to auto-deploy to the server.
