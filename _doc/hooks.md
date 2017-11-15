Hooks
=====

- `_main.template.shortcuts` - must echo the output
- `_main.browse.thead` - must echo the output
- `_main.browse.tbody` - passed the full share path of the file or folder, must echo the output
- `_main.browse.head` - must echo output. note: nested lists currently look horrible
- `_main.login.message` - must echo the output
- `_main.login.post_form` - must echo the output
- `_main.login.form` - must echo the output
- `_main.admin.page` - must echo the output as "li"s
- `_main.properties.propertytable` - must echo the output as tr's and td's
- `_main.settings.easysetting` - must echo the output as a form, see settings.php for examples
- `_main.about.post_license` - must echo the output, should have an &lt;hr /&gt; at the end
