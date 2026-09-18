/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{ts,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      colors: {
        navy: {
          950: '#0A1628',
          900: '#0F1E33',
          800: '#152A44',
        },
        brand: {
          50: '#EFF6FF',
          100: '#DBEAFE',
          400: '#4FA3F7',
          500: '#2563EB',
          600: '#1D4ED8',
          700: '#1E40AF',
        },
        ink: {
          900: '#0B1E33',
          700: '#334155',
          500: '#64748B',
          300: '#CBD5E1',
        },
      },
      boxShadow: {
        card: '0 1px 2px rgba(15, 30, 51, 0.04), 0 8px 24px -12px rgba(15, 30, 51, 0.12)',
        cardHover: '0 4px 12px rgba(15, 30, 51, 0.06), 0 16px 32px -12px rgba(37, 99, 235, 0.18)',
        float: '0 20px 60px -20px rgba(15, 30, 51, 0.25)',
      },
      borderRadius: {
        xl2: '1.25rem',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-10px)' },
        },
        charge: {
          '0%': { width: '10%' },
          '50%': { width: '90%' },
          '100%': { width: '10%' },
        },
      },
      animation: {
        float: 'float 5s ease-in-out infinite',
        charge: 'charge 3s ease-in-out infinite',
      },
    },
  },
  plugins: [],
}
