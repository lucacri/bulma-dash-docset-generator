# Bulma docset generator for Dash

Unfortunately Bulma does not offer a docset for Dash yet.

I created this Laravel app to generate one for you. It will use `wget` and `dashing` to create it.

To run it, just clone the repository and run `php artisan bulma:download-and-generate`. It will create a folder called `bulma.docset` in the folder `output`.
