jQuery(function($){
    $('form#profile-form').on('submit', function(e){
        e.preventDefault();
        let formData = {
            action: 'simple_auth_profile_form',
            display_name: $('input[name="display_name"]').val(),
            email: $('input[name="email"]').val(),
            _ajax_nonce: dataBank.ajax_nonce
        };
       $.ajax({
           url: dataBank.ajax_url,
           method: 'POST',
           data: formData,
           success: function(response){
            console.log(response);
           }
       });
    });
});