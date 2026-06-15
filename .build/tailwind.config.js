module.exports = {
  important: '#pkwt-dashboard-root',
  corePlugins: { preflight: false },   // never reset native wp-admin styles
  content: ['/tmp/ppbuild/pkwt-dashboard.compiled.js'],
  theme: {
    extend: {
      fontFamily: { sans:['Inter','system-ui','sans-serif'], mono:['JetBrains Mono','ui-monospace','monospace'] },
      colors: {
        bg:       'rgb(var(--c-bg) / <alpha-value>)',
        card:     'rgb(var(--c-card) / <alpha-value>)',
        border:   'rgb(var(--c-border) / <alpha-value>)',
        sidebar:  'rgb(var(--c-sidebar) / <alpha-value>)',
        sub:      'rgb(var(--c-sub) / <alpha-value>)',
        fg:       'rgb(var(--c-fg) / <alpha-value>)',
        brand:    '#FF6500',
        brandDark:'#cc5200',
        ok:       '#3fb950',
      },
      boxShadow: {
        card: 'var(--shadow-card)',
        glow: '0 0 0 1px rgba(255,101,0,.4),0 0 32px rgba(255,101,0,.18)',
      },
    },
  },
}
