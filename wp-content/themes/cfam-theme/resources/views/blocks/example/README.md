# Example block template

Use the files in this directory as a template for creating new blocks.

## example.php

### What to change:

[ ] Line 3, machine name: lower case, no spaces, hyphenated. The machine name needs to match the template name.
[ ] Line 4, human-friendly name, used as the Gutenberg block label
[ ] Line 6, name of the callback function that displays the block (see line 18). This should be lowercase, no spaces, underscored.
[ ] Line 7, choose appropriate category this block belongs to
[ ] Line 8, icon. See https://developer.wordpress.org/resource/dashicons. Use the name of the icon without the prefixing `dashicons-` string.
[ ] Line 9, any appropriate keywords to help editors search for blocks inside Wordpress.
[ ] Lines 11-18, some additional options for block behavior inside Wordpress.
[ ] Line 18, name of the callback function, see line 6.
[ ] Lines 41-48, you can write any PHP function and pass it to the template like this. Delete this if you don't need it.
[ ] Line 51, name of the relative twig file to render the block. It must match the machine name.

### Notes:

The `is_preview` variable will allow you to render content differently inside the Twig file, if you wish. In most cases we don't make use of this feature, but it's there if you'd like to use it.

## example.twig

### What to change:

[ ] Line 1, use the machine name for the `data-module` attribute. This can be used for styling and/or scripting.

### Notes:

-   All the appropriate ACF fields are inside the `fields` namespace. You can grab your fields like this: `{fields.field_name}`. If you're unsure where your data lives, you can use `{{ dump(fields) }}` to explore what's available.
-   For more information, reference Timber's ACF Cookbook: https://timber.github.io/docs/guides/acf-cookbook/

## Wire it up

To make your block known to ACF so you can add fields to it, add its machine name to the `$registered_blocks` array inside the `register_acf_blocks` function in `app/plugins/acf/index.php`.
