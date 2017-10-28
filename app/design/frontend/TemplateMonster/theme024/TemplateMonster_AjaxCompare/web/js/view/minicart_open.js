define(["jquery/ui","jquery"], function(Component, $){
    return function(config, element){
        var minicart = $(element);
        $('#go-to-cart').on( "click", function() {
            minicart.find('[data-role="dropdownDialog"]').dropdownDialog("open");
        });
    }
});