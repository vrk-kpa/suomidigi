All style changes are done in the src/scss folder and generated into css automatically using gulp.

Note!
None of the dist files are included in git, which means less conflicts of compiled css files.
The dist files are compiled when deploying to test/production.

For everything to work correctly you need to install required modules. Run the following:

    npm install

After this you can run the command that compiles your sass files to css. It will compile the css into human-readable form and begins watching the scss/js source folders.

    npm run gulp

If you just want to compile the sass and js, you can run:

    npm run gulp development

If you want to test how the css and js is compiled in production, you can run:

    npm run gulp production
