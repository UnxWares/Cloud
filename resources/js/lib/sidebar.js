import { writable } from 'svelte/store'

// Store pour l'état collapsed de la sidebar
export const sidebarCollapsed = writable(false)

// Largeur de la sidebar selon l'état
export const SIDEBAR_WIDTH_EXPANDED = 240
export const SIDEBAR_WIDTH_COLLAPSED = 60
