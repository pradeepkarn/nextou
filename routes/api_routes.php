<?php
$api_routes = [
    "/api/v1/account/create/{ug}" => 'Users_api@create_account@name.createAccountApi',
    "/api/v1/account/update/{ug}" => 'Users_api@update_account@name.updateAccountApi',
    "/api/v1/account/login/{ug}" => 'Users_api@login@name.loginAccountApi',
    "/api/v1/account/login-via-token/{ug}" => 'Users_api@login_via_token@name.loginAccountViaTokenApi',
    
    ####################################################################################################

    "/api/v1/product/list-categories" =>'Product_api@list_categories@name.productCatListAPi',
    "/api/v1/product/list" =>'Product_api@list@name.productListAPi',
    "/api/v1/product/list-favs" =>'Product_api@list_favs@name.productFavListAPi',
    "/api/v1/product/details/{id}" =>'Product_api@details@name.producDetailsAPi',

    "/api/v1/product/create" =>'Product_api@create@name.productCreateApi',
    "/api/v1/product/update" =>'Product_api@update@name.productUpdateApi',
    "/api/v1/product/create-review" =>'Review_api@create@name.reviewCreateApi',
    "/api/v1/product/mark-fav-unfav" =>'Product_api@mark_as_favunfav@name.favUnfavApi',
    "/api/v1/product/list-my-products" =>'Product_api@list_my_products@name.listMyProdsApi',
    // logs
    '/api/v1/log/list' => 'Logs_api@list@name.logListApi',
    // chats
    '/api/v1/chat/history' => 'Product_api@chat_hist_api@name.chatHistoryApi',    
    '/api/v1/chat/save' => 'Product_api@chat_save_api@name.chatSaveApi',    
];