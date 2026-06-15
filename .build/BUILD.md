# Build process (dashboard assets)

Source: `assets/js/pkwt-dashboard.js` (JSX). Compiled outputs are committed so no build
is needed at install time.

```
npm install @babel/core @babel/cli @babel/preset-react tailwindcss@3.4.17
# 1. JSX -> plain JS (classic runtime, uses the global React we vendor)
babel --config-file .build/babelrc.json assets/js/pkwt-dashboard.js -o assets/js/pkwt-dashboard.min.js
# 2. Tailwind -> static CSS (preflight off, scoped to #pkwt-dashboard-root)
tailwindcss -c .build/tailwind.config.js -i in.css -o assets/css/pkwt-tailwind.css --minify
```
React/ReactDOM 18.3.1 production UMD builds are vendored in `assets/vendor/`.
