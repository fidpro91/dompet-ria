$(document).ready(function() {
    $.fn.startLoading = function() {
        return this.each(function() {
            var button = $(this);
            button.prop('disabled', true);
            
            var originalText = button.text().trim();
            button.data('original-text', originalText);

            var loadingClass = button.attr('class-loading');
            var spinner = $('<span>').addClass(loadingClass).attr('role', 'status').attr('aria-hidden', 'true');
            button.html(spinner).append(" Loading...");
        });
    };
    
    $.fn.stopLoading = function() {
        return this.each(function() {
            var button = $(this);
            button.prop('disabled', false);
            var originalText = button.data('original-text');
            button.html(originalText);
        });
    };
});
