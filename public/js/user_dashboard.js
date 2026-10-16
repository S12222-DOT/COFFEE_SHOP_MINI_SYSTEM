// Coffee Shop Application
class CoffeeShop {
  constructor() {
    this.cart = []
    this.favorites = new Set()
    this.currentCategory = "coffee"
    this.taxRate = 0.1

    this.init()
  }

  init() {
    this.setupEventListeners()
    this.initializeTooltips()
    this.filterProducts(this.currentCategory)
  }

  setupEventListeners() {
    // Category tabs
    document.querySelectorAll(".category-tab").forEach((tab) => {
      tab.addEventListener("click", (e) => this.handleCategoryChange(e))
    })

    // Add to cart buttons
    document.querySelectorAll(".add-to-cart-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => this.handleAddToCart(e))
    })

    // Favorite buttons
    document.querySelectorAll(".favorite-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => this.handleFavoriteToggle(e))
    })

    // Pay now button
    // const payNowBtn = document.getElementById("payNowBtn")
    // if (payNowBtn) {
    //   payNowBtn.addEventListener("click", () => this.handlePayment())
    // }

    // Search functionality
    const searchInput = document.querySelector(".search-input")
    if (searchInput) {
      searchInput.addEventListener("input", (e) => this.handleSearch(e))
    }
  }

  initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map((tooltipTriggerEl) => new window.bootstrap.Tooltip(tooltipTriggerEl))
  }

  handleCategoryChange(e) {
    const category = e.target.dataset.category

    // Update active tab with animation
    document.querySelectorAll(".category-tab").forEach((tab) => {
      tab.classList.remove("active")
    })
    e.target.classList.add("active")

    // Filter products
    this.currentCategory = category
    this.filterProducts(category)
  }

  filterProducts(category) {
    const products = document.querySelectorAll(".product-card")

    products.forEach((product, index) => {
      const productCategory = product.dataset.category

      if (productCategory === category) {
        product.classList.remove("hidden")
        // Stagger animation
        setTimeout(() => {
          product.style.animation = "none"
          setTimeout(() => {
            product.style.animation = "slideUp 0.5s ease"
          }, 10)
        }, index * 50)
      } else {
        product.classList.add("hidden")
      }
    })
  }

  handleAddToCart(e) {
    const btn = e.currentTarget
    const productId = btn.dataset.productId
    const productName = btn.dataset.productName
    const productPrice = Number.parseFloat(btn.dataset.productPrice)
    const productImage = btn.dataset.productImage

    // Add animation to button
    btn.style.transform = "scale(0.8) rotate(90deg)"
    setTimeout(() => {
      btn.style.transform = ""
    }, 200)

    // Check if product already in cart
    const existingItem = this.cart.find((item) => item.id === productId)

    if (existingItem) {
      existingItem.quantity++
    } else {
      this.cart.push({
        id: productId,
        name: productName,
        price: productPrice,
        image: productImage,
        quantity: 1,
      })
    }

    this.updateCart()
  }

  handleFavoriteToggle(e) {
    const btn = e.currentTarget
    const productId = btn.dataset.productId

    // Toggle favorite
    if (this.favorites.has(productId)) {
      this.favorites.delete(productId)
      btn.classList.remove("active")
    } else {
      this.favorites.add(productId)
      btn.classList.add("active")

      // Heart animation
      btn.style.animation = "none"
      setTimeout(() => {
        btn.style.animation = "heartBeat 0.5s ease"
      }, 10)
    }
  }

  updateCart() {
    const orderItems = document.getElementById("orderItems")
    const orderSummary = document.getElementById("orderSummary")

    if (this.cart.length === 0) {
      orderItems.innerHTML = '<div class="empty-cart"><p>Your cart is empty</p></div>'
      orderSummary.style.display = "none"
      return
    }

    // Render cart items
    orderItems.innerHTML = this.cart
      .map(
        (item) => `
            <div class="order-item">
                <img src="${item.image}" alt="${item.name}" class="order-item-image">
                <div class="order-item-details">
                    <div class="order-item-name">${item.name}</div>
                    <div class="order-item-quantity">(x${item.quantity})</div>
                </div>
                <div class="order-item-price">$${(item.price * item.quantity).toFixed(2)}</div>
                <button class="order-item-remove" onclick="coffeeShop.removeFromCart('${item.id}')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        `,
      )
      .join("")

    // Calculate totals
    const subtotal = this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0)
    const tax = subtotal * this.taxRate
    const total = subtotal + tax

    // Update summary
    document.getElementById("subtotal").textContent = `$${subtotal.toFixed(2)}`
    document.getElementById("tax").textContent = `$${tax.toFixed(3)}`
    document.getElementById("total").textContent = `$${total.toFixed(3)}`

    orderSummary.style.display = "block"
  }

  removeFromCart(productId) {
    this.cart = this.cart.filter((item) => item.id !== productId)
    this.updateCart()
  }

  handlePayment() {
    if (this.cart.length === 0) return

    const total = this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0) * (1 + this.taxRate)

    // Show payment animation
    // const payBtn = document.getElementById("payNowBtn")
    // payBtn.textContent = "Processing..."
    // payBtn.disabled = true

    // setTimeout(() => {
    //   alert(`Payment successful! Total: $${total.toFixed(2)}\n\nThank you for your order!`)
    //   this.cart = []
    //   this.updateCart()
    //   payBtn.textContent = "Pay Now"
    //   payBtn.disabled = false
    // }, 1500)
   
  }

  handleSearch(e) {
    const searchTerm = e.target.value.toLowerCase()
    const products = document.querySelectorAll(".product-card")

    products.forEach((product) => {
      const productName = product.querySelector(".product-name").textContent.toLowerCase()
      const productCategory = product.dataset.category

      if (productName.includes(searchTerm) && productCategory === this.currentCategory) {
        product.classList.remove("hidden")
      } else {
        product.classList.add("hidden")
      }
    })
  }
}

// Add heart beat animation
const style = document.createElement("style")
style.textContent = `
    @keyframes heartBeat {
        0%, 100% { transform: scale(1); }
        25% { transform: scale(1.3); }
        50% { transform: scale(1.1); }
        75% { transform: scale(1.25); }
    }
`
document.head.appendChild(style)

// Initialize the app
let coffeeShop
document.addEventListener("DOMContentLoaded", () => {
  coffeeShop = new CoffeeShop()
})
