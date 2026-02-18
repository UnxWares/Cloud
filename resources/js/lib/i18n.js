import { writable, derived } from 'svelte/store'

// Langue par défaut
const DEFAULT_LOCALE = 'fr'

// Stocker la langue actuelle
function createLocaleStore() {
  const getInitialLocale = () => {
    if (typeof window === 'undefined') return DEFAULT_LOCALE

    // Vérifier si une langue est sauvegardée
    const stored = localStorage.getItem('locale')
    if (stored && ['fr', 'en'].includes(stored)) {
      return stored
    }

    // Sinon, utiliser la langue du navigateur
    const browserLang = navigator.language.split('-')[0]
    return ['fr', 'en'].includes(browserLang) ? browserLang : DEFAULT_LOCALE
  }

  const initialLocale = getInitialLocale()
  const { subscribe, set } = writable(initialLocale)

  return {
    subscribe,
    set: (value) => {
      if (typeof window !== 'undefined') {
        localStorage.setItem('locale', value)
        document.documentElement.lang = value
      }
      set(value)
    },
    init: () => {
      if (typeof document !== 'undefined') {
        document.documentElement.lang = initialLocale
      }
    }
  }
}

export const locale = createLocaleStore()
locale.init()

// Traductions
const translations = {
  fr: {
    // Navigation
    'nav.dashboard': 'Dashboard',
    'nav.services': 'Services',
    'nav.settings': 'Paramètres',
    'nav.lightMode': 'Mode clair',
    'nav.darkMode': 'Mode sombre',
    'nav.backHome': 'Retour à l\'accueil',
    'nav.toggleServices': 'Afficher/masquer les services {name}',
    'nav.activateLightMode': 'Activer le mode clair',
    'nav.activateDarkMode': 'Activer le mode sombre',

    // Dashboard
    'dashboard.title': 'Déploiements',
    'dashboard.subtitle': 'Gérez tous vos services déployés',
    'dashboard.search': 'Rechercher un déploiement...',
    'dashboard.noDeployments': 'Aucun déploiement trouvé',

    // Filtres
    'filter.allPacks': 'Tous les packs',
    'filter.allServices': 'Tous les services',
    'filter.allStatuses': 'Tous les statuts',
    'filter.allDatacenters': 'Tous les datacenters',
    'filter.byService': 'Filtrer par service',
    'filter.byStatus': 'Filtrer par statut',
    'filter.byDatacenter': 'Filtrer par datacenter',
    'filter.searchDeployment': 'Rechercher un déploiement',

    // Statuts
    'status.running': 'En cours',
    'status.stopped': 'Arrêté',
    'status.pending': 'En attente',
    'status.error': 'Erreur',

    // Stats
    'stats.total': 'Total',
    'stats.running': 'En cours',
    'stats.datacenters': 'Datacenters',

    // Services
    'services.noServices': 'Aucun service disponible',
    'services.selectPack': 'Sélectionner un pack',
    'services.showAll': 'Afficher tous les déploiements',

    // Commun
    'common.loading': 'Chargement...',
    'common.error': 'Erreur',
    'common.retry': 'Réessayer',
  },

  en: {
    // Navigation
    'nav.dashboard': 'Dashboard',
    'nav.services': 'Services',
    'nav.settings': 'Settings',
    'nav.lightMode': 'Light mode',
    'nav.darkMode': 'Dark mode',
    'nav.backHome': 'Back to home',
    'nav.toggleServices': 'Show/hide {name} services',
    'nav.activateLightMode': 'Activate light mode',
    'nav.activateDarkMode': 'Activate dark mode',

    // Dashboard
    'dashboard.title': 'Deployments',
    'dashboard.subtitle': 'Manage all your deployed services',
    'dashboard.search': 'Search deployment...',
    'dashboard.noDeployments': 'No deployments found',

    // Filtres
    'filter.allPacks': 'All packs',
    'filter.allServices': 'All services',
    'filter.allStatuses': 'All statuses',
    'filter.allDatacenters': 'All datacenters',
    'filter.byService': 'Filter by service',
    'filter.byStatus': 'Filter by status',
    'filter.byDatacenter': 'Filter by datacenter',
    'filter.searchDeployment': 'Search deployment',

    // Statuts
    'status.running': 'Running',
    'status.stopped': 'Stopped',
    'status.pending': 'Pending',
    'status.error': 'Error',

    // Stats
    'stats.total': 'Total',
    'stats.running': 'Running',
    'stats.datacenters': 'Datacenters',

    // Services
    'services.noServices': 'No services available',
    'services.selectPack': 'Select a pack',
    'services.showAll': 'Show all deployments',

    // Commun
    'common.loading': 'Loading...',
    'common.error': 'Error',
    'common.retry': 'Retry',
  }
}

// Fonction de traduction avec support de variables
export const t = derived(locale, ($locale) => {
  return (key, vars = {}) => {
    let translation = translations[$locale]?.[key] || translations[DEFAULT_LOCALE]?.[key] || key

    // Remplacer les variables {name} par leurs valeurs
    Object.keys(vars).forEach(varKey => {
      translation = translation.replace(`{${varKey}}`, vars[varKey])
    })

    return translation
  }
})

// Fonction helper pour changer de langue
export function setLocale(newLocale) {
  if (['fr', 'en'].includes(newLocale)) {
    locale.set(newLocale)
  }
}

// Liste des langues disponibles
export const availableLocales = [
  { code: 'fr', name: 'Français' },
  { code: 'en', name: 'English' }
]
