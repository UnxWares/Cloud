import { writable } from 'svelte/store'

// Récupérer le thème depuis localStorage ou utiliser le thème système
const getInitialTheme = () => {
  if (typeof window === 'undefined') return 'light'

  const stored = localStorage.getItem('theme')
  if (stored) return stored

  // Utiliser la préférence système
  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
}

// Fonction pour appliquer le thème au document
const applyTheme = (value) => {
  if (typeof window !== 'undefined' && typeof document !== 'undefined') {
    localStorage.setItem('theme', value)
    if (value === 'dark') {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }
}

// Initialiser le thème immédiatement
const initialTheme = getInitialTheme()
applyTheme(initialTheme)

export const theme = writable(initialTheme)

// Appliquer le thème à chaque changement
theme.subscribe(value => {
  applyTheme(value)
})

export const toggleTheme = () => {
  theme.update(current => current === 'light' ? 'dark' : 'light')
}
