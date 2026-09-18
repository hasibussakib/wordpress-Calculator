# WordPress upload zip

`batterysizing.zip` is the file to upload in **Appearance → Themes → Upload Theme**.

Structure (required by WordPress):

```
batterysizing.zip
└── batterysizing/
    ├── style.css      ← Theme Name header
    ├── functions.php
    └── …
```

Do **not** upload GitHub’s “Code → Download ZIP” of the whole repository — that nests `style.css` one folder too deep and WordPress reports “The theme is missing the style.css stylesheet.”
