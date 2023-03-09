document.addEventListener('alpine:init', () => {
    Alpine.data('refreshOffres', () => ({
            currentCategory: 0,
            isLoading: false,
            articles: [],
            init() {
                this.isLoading = true
                this.articles = []
            },
            async initOffres(categoryId) {
                this.isLoading = true
                this.currentCategory = categoryId
             this.articles =   this.launchRefresh(null)
            },
            async changeOffres(f) {
                this.currentCategory = f
                this.launchRefresh(null)
            },
            async launchRefresh(e) {
                this.isLoading = true
                if (e !== null) {
                    this.currentCategory = e.target.dataset.filtre
                }
                console.log(this.currentCategory)
                const url = `https://www.marche.be/wp-json/jfs/v1/all/${this.currentCategory}`
                console.log(url)
                return   await fetch(url)
                    .then(function (response) {
                        // The API call was successful!
                        return response.json()
                    })
                    .then(function (data) {
                        // This is the JSON from our response
                        return data
                    })
                    .catch(function (err) {
                        // There was an error
                        this.isLoading = false
                        console.warn("Something went wrong.", err)
                        return err
                    })
                this.isLoading = false
            }
        })
    )
})