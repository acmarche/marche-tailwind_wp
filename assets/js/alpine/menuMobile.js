document.addEventListener( 'alpine:init', () => {
    Alpine.data( 'menuMobile', () => ( {
            menuMobileIsOpen: false,
            searchMobileIsOpen: false,
            sousMenuOpen: 0,
            isLoading: false,
            init() {
                this.sousMenuOpen = 0
                this.menuMobileIsOpen = false
                this.searchMobileIsOpen = false
                this.sousMenuOpen = 0
            }
        } )
    )
} )