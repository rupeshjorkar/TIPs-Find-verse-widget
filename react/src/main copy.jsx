import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import './index.css'

function initApp() {
  const container = document.getElementById('react-root')
  if (container && !container.hasAttribute('data-react-init')) {
    const root = ReactDOM.createRoot(container)
    root.render(<App />)
    container.setAttribute('data-react-init', 'true')
  }
}

// Initialize when ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp)
} else {
  initApp()
}

// Re-init for dynamic content
const observer = new MutationObserver(() => initApp())
observer.observe(document.body, { childList: true, subtree: true })