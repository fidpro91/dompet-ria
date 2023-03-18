'use strict';

(function () {
    function init() {
        var router = new Router([
            new Route('home', 'homea.html', true),            
            new Route('about', 'about.html'),
            new Route('contoh', 'contoh.html'),
        ]);
    }
    init();
}());
