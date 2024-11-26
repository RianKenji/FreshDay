document.addEventListener('alpine:init', () => {
    Alpine.data('products', () => ({
      items: [
        { id: 1, name: 'Robusta Brazil', img: '1.jpg', price: 20_000 },
        { id: 2, name: 'Arabica Blend', img: '1.jpg', price: 25_000 },
        { id: 3, name: 'Primo Passo', img: '1.jpg', price: 30_000 },
        { id: 4, name: 'Aceh Gayo', img: '1.jpg', price: 35_000 },
        { id: 5, name: 'Sumatra Mandheling', img: '1.jpg', price: 40_000 },
      ],
    }))
  
    Alpine.store('cart', {
      items: [],
      quantity: 0,
      total: 0,
      add(newItem) {
        const cartItem = this.items.find((item) => item.id === newItem.id)
  
        if (!cartItem) {
          this.items = [
            ...this.items,
            { ...newItem, quantity: 1, total: newItem.price },
          ]
          this.quantity++
          this.total += newItem.price
        } else {
          this.items = this.items.map((item) =>
            item.id === newItem.id
              ? {
                  ...item,
                  quantity: item.quantity + 1,
                  total: item.price * (item.quantity + 1),
                }
              : item
          )
          this.quantity++
          this.total += cartItem.price
        }
      },
      remove(id) {
        const cartItem = this.items.find((item) => item.id === id)
  
        if (!cartItem) return
  
        if (cartItem.quantity > 1) {
          this.items = this.items.map((item) =>
            item.id === id
              ? {
                  ...item,
                  quantity: item.quantity - 1,
                  total: item.price * (item.quantity - 1),
                }
              : item
          )
          this.quantity--
          this.total -= cartItem.price
        } else {
          this.items = this.items.filter((item) => item.id !== id)
          this.quantity--
          this.total -= cartItem.price
        }
      },
    })
  })