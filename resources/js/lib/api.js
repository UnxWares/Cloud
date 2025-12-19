class ApiService {
  constructor() {
    // API Laravel locale (proxy vers l'orchestrateur)
    this.baseUrl = '/api'
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`

    try {
      const response = await fetch(url, {
        ...options,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          ...options.headers,
        },
      })

      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}))
        throw new Error(errorData.message || `HTTP error! status: ${response.status}`)
      }

      return await response.json()
    } catch (error) {
      console.error('API Error:', error)
      throw error
    }
  }

  // ========================================
  // CATALOGUE (routes publiques)
  // ========================================

  // Lister tous les packs
  async getServicePacks() {
    return this.request('/service-packs')
  }

  // Lister les services d'un pack
  async getPackServices(packSlug) {
    return this.request(`/service-packs/${packSlug}`)
  }

  // Lister les offres d'un service
  async getServiceOffers(packSlug, serviceSlug) {
    return this.request(`/services?pack_slug=${packSlug}&service_slug=${serviceSlug}`)
  }

  // Détails d'une offre
  async getOfferDetails(packSlug, serviceSlug, offerId) {
    return this.request(`/services/${offerId}?pack_slug=${packSlug}&service_slug=${serviceSlug}`)
  }

  // ========================================
  // DEPLOYMENTS (routes avec JWT)
  // ========================================

  // Tous mes déploiements
  async getDeployments() {
    return this.request('/deployments')
  }

  // Mes déploiements d'un service spécifique
  async getServiceDeployments(packSlug, serviceSlug) {
    return this.request(`/deployments?pack_slug=${packSlug}&service_slug=${serviceSlug}`)
  }

  // Détails d'un déploiement
  async getDeploymentDetails(packSlug, serviceSlug, deploymentId) {
    return this.request(`/deployments/${deploymentId}?pack_slug=${packSlug}&service_slug=${serviceSlug}`)
  }
}

export const api = new ApiService()
