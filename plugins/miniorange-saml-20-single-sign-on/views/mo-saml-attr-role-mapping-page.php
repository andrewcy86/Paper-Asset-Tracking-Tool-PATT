<?php


function mo_saml_save_optional_config()
{
    global $wpdb;
    $vj = get_option("\x65\156\x74\x69\164\x79\x5f\151\x64");
    if ($vj) {
        goto kU;
    }
    $vj = "\150\x74\164\160\163\72\57\57\x6c\157\x67\x69\x6e\56\x78\x65\143\165\162\151\x66\171\x2e\143\x6f\155\x2f\155\157\x61\x73";
    kU:
    $Tg = get_option("\x73\x73\157\x5f\165\162\x6c");
    $zP = get_option("\x63\145\162\x74\137\x66\x70");
    $Qz = get_option("\x73\141\x6d\154\137\x69\144\145\156\x74\x69\164\x79\137\x6e\141\155\x65");
    $nN = get_option("\x73\x61\x6d\154\137\x61\155\x5f\x75\x73\x65\162\x6e\x61\x6d\x65");
    if (!($nN == NULL)) {
        goto Xg;
    }
    $nN = "\116\x61\x6d\145\x49\x44";
    Xg:
    $W_ = get_option("\x73\x61\x6d\154\137\x61\155\x5f\x65\155\141\151\x6c");
    if (!($W_ == NULL)) {
        goto B1;
    }
    $W_ = "\x4e\x61\x6d\x65\111\104";
    B1:
    $k1 = get_option("\x73\x61\x6d\154\137\141\155\x5f\146\x69\162\163\x74\x5f\x6e\141\x6d\x65");
    $Wj = get_option("\163\x61\x6d\x6c\137\x61\155\137\154\141\163\164\x5f\x6e\x61\155\145");
    $An = get_option("\163\x61\x6d\154\137\141\155\137\147\162\157\165\160\x5f\x6e\x61\x6d\145");
    $xi = get_option("\155\157\137\x73\x61\x6d\x6c\x5f\164\x65\163\x74\137\x63\157\x6e\146\151\x67\x5f\x61\164\x74\162\x73");
    $xi = maybe_unserialize($xi);
    $current_user = wp_get_current_user();
    $eX = get_user_meta($current_user->ID);
    echo "\15\12\11\11\x3c\146\157\162\x6d\40\x6e\x61\x6d\145\75\42\x73\141\x6d\x6c\x5f\146\x6f\162\155\x5f\141\155\42\40\167\151\144\x74\x68\75\x22\x39\x38\x25\42\40\x62\157\x72\144\145\162\x3d\42\x30\x22\x20\x73\x74\171\154\x65\75\x22\x62\x61\x63\x6b\x67\162\x6f\x75\x6e\x64\x2d\143\157\154\157\x72\x3a\43\106\x46\x46\106\x46\106\x3b\40\x62\x6f\x72\x64\x65\x72\72\61\x70\x78\40\x73\157\x6c\151\144\x20\x23\103\103\103\103\x43\103\73\40\x70\x61\x64\x64\x69\156\147\72\60\x70\170\40\x30\x70\x78\40\60\x70\170\x20\61\60\160\170\x3b\42\40\x6d\145\x74\x68\x6f\x64\75\42\x70\x6f\163\x74\42\x20\x61\x63\x74\151\x6f\156\x3d\42\x22\x3e";
    wp_nonce_field("\x6c\x6f\x67\151\156\x5f\x77\x69\x64\x67\x65\164\x5f\x73\141\155\154\137\141\x74\164\x72\x69\142\x75\164\145\137\155\x61\x70\x70\151\x6e\x67");
    echo "\x3c\151\156\x70\x75\164\40\164\x79\160\x65\x3d\42\x68\x69\x64\144\145\156\42\x20\x6e\141\x6d\145\75\42\x6f\160\164\151\x6f\x6e\x22\40\166\x61\x6c\x75\145\x3d\x22\x6c\157\x67\x69\156\137\167\x69\x64\147\x65\x74\x5f\x73\x61\x6d\154\x5f\x61\x74\x74\162\x69\x62\x75\164\x65\137\155\141\160\x70\x69\x6e\x67\x22\x20\57\x3e\15\12\x9\x9\74\150\63\x3e\x41\164\x74\x72\x69\x62\x75\164\x65\40\x4d\141\x70\x70\x69\x6e\x67\x3c\57\x68\63\76\x3c\x68\162\x3e\xd\xa\11\x9\74\x64\x69\166\x20\x73\x74\171\154\145\x3d\42\x6d\141\x72\x67\151\x6e\72\62\x25\x20\60\x20\62\45\40\x31\x30\x70\170\73\40\160\x61\x64\144\x69\x6e\x67\55\142\157\x74\164\157\155\x3a\62\x2e\70\145\x6d\x22\x3e\xd\12\x9\11\74\x74\x61\x62\154\145\40\151\x64\75\42\x6d\171\124\x61\142\154\145\x22\x20\167\151\x64\164\x68\x3d\x22\x31\60\x30\45\42\76\xd\xa\x9\11\x20\40\x3c\164\162\x3e\15\12\x9\x9\11\x3c\164\144\x20\x63\157\x6c\x73\x70\x61\156\x3d\42\x32\42\76\15\xa\11\x9\x9\x9\xd\xa\11\11\11\74\x2f\164\144\x3e\15\12\11\11\x20\40\74\57\164\x72\x3e";
    check_plugin_state();
    echo "\x3c\x74\x72\76\15\xa\x9\x9\x20\40\x9\x3c\x74\x64\40\143\x6f\x6c\x73\x70\x61\x6e\75\x22\x32\x22\x3e\133\x20\x3c\141\40\164\x61\x72\x67\x65\x74\75\42\137\142\154\141\156\153\42\x20\150\162\x65\x66\x3d\x22\x68\x74\x74\x70\x73\x3a\x2f\57\144\x6f\x63\163\x2e\155\151\x6e\151\x6f\x72\141\156\x67\145\x2e\143\157\x6d\57\144\157\x63\x75\155\x65\156\164\x61\164\x69\x6f\x6e\57\163\141\155\x6c\x2d\150\141\156\144\142\x6f\x6f\x6b\57\x61\x74\x74\162\151\x62\165\164\145\x2d\162\x6f\x6c\x65\x2d\x6d\141\x70\160\151\156\147\57\x61\164\x74\162\x69\x62\x75\164\x65\x2d\x6d\141\160\x70\x69\x6e\x67\x22\x3e\103\154\x69\x63\x6b\x20\110\x65\162\145\74\x2f\x61\x3e\x20\x74\x6f\x20\153\156\x6f\167\x20\155\157\x72\x65\40\141\x62\x6f\165\x74\x20\164\x68\x69\163\x20\146\145\x61\x74\x75\x72\145\x2e\x20\135\xd\xa\x9\x9\x20\11\xd\xa\x9\x9\11\11\74\57\x74\x64\x3e\15\xa\x9\11\x20\40\x3c\x2f\164\x72\x3e\15\xa\11\x9\40\x20\74\x74\162\x3e\15\xa\11\x9\11\x3c\x74\x64\76\74\x62\162\57\x3e\x3c\x2f\164\x64\76\11\x20\xd\xa\11\11\x20\40\74\x2f\x74\162\x3e";
    echo "\x3c\x74\x72\76\15\12\x9\x9\x9\x9\x3c\x74\144\40\x73\x74\x79\x6c\x65\75\42\167\x69\144\x74\150\x3a\61\65\x30\160\170\73\42\76\74\163\x74\162\157\156\147\x3e\x55\x73\x65\162\x6e\x61\155\145\x20\x3c\x73\x70\141\156\40\163\x74\171\x6c\x65\75\42\x63\157\154\157\x72\x3a\x72\145\144\x3b\42\x3e\x2a\x3c\57\x73\160\x61\156\76\x3a\x3c\57\x73\x74\x72\x6f\x6e\147\76\x3c\57\164\x64\76\xd\12\11\x9\11\11\x3c\164\144\x3e";
    if ($xi and is_array($xi)) {
        goto sA;
    }
    echo "\74\x69\156\160\165\164\40\x74\171\160\x65\x3d\42\x74\x65\x78\164\42\x20\156\141\x6d\x65\x3d\42\x73\x61\x6d\154\x5f\x61\x6d\x5f\165\x73\145\162\156\141\155\145\42\40\x70\x6c\x61\143\x65\x68\157\x6c\144\x65\162\75\42\105\x6e\x74\x65\x72\40\x61\164\164\162\151\142\165\164\145\40\x6e\x61\x6d\145\40\x66\x6f\x72\x20\125\163\145\x72\156\141\x6d\145\x22\40\163\x74\171\154\145\75\42\x77\x69\x64\x74\x68\x3a\x61\165\x74\157\73\40\x6d\151\x6e\55\x77\151\144\164\150\72\65\x30\45\73\40\155\x61\x78\x2d\x77\x69\144\x74\x68\72\x38\60\x25\x22\40\166\141\154\x75\x65\75\x22" . $nN . "\x22\x20\162\145\161\x75\151\162\145\x64";
    if (mo_saml_is_customer_license_key_verified()) {
        goto Lg;
    }
    echo "\x64\151\x73\x61\x62\154\x65\144";
    Lg:
    echo "\x2f\76";
    goto R9;
    sA:
    echo "\x3c\163\x65\x6c\x65\143\164\40\x6e\x61\155\145\x3d\x22\x73\141\x6d\154\x5f\141\x6d\x5f\165\x73\145\162\x6e\141\155\x65\x22\x20\x73\164\171\154\x65\75\x22\x77\x69\x64\164\150\x3a\x61\165\x74\x6f\x3b\x20\x6d\x69\x6e\x2d\x77\151\144\164\x68\x3a\x35\60\45\73\40\x6d\x61\170\x2d\x77\151\144\x74\150\72\70\60\45\x22";
    if (mo_saml_is_customer_license_key_verified()) {
        goto Pk;
    }
    echo "\144\151\x73\141\142\154\145\x64";
    Pk:
    echo "\x3e\15\xa\11\x9\11\x9\x9\74\x6f\x70\164\151\157\156\x20\166\141\154\x75\145\75\42\42\x3e\55\x2d\x53\145\154\145\x63\x74\40\141\x6e\x20\101\x74\x74\162\151\x62\x75\164\145\55\55\74\x2f\x6f\x70\164\x69\x6f\156\76";
    foreach ($xi as $ld => $g2) {
        $rr = $nN == $ld ? "\x73\x65\x6c\145\x63\164\x65\x64" : '';
        echo "\74\x6f\160\164\x69\157\x6e\x20\166\x61\x6c\x75\145\75\x22" . $ld . "\x22\x20" . $rr . "\x20\x3e" . $ld . "\x3c\x2f\x6f\160\x74\151\x6f\x6e\x3e";
        S6:
    }
    gH:
    echo "\74\57\x73\x65\x6c\145\x63\164\x3e";
    R9:
    echo "\74\57\x74\x64\x3e\15\xa\11\11\x9\40\x20\74\57\x74\162\76\xd\xa\x9\11\11\x20\x20\x3c\x74\x72\76\xd\12\11\x9\x9\x9\x3c\x74\144\x3e\74\163\x74\162\x6f\x6e\147\76\x45\155\141\151\x6c\40\x3c\163\160\x61\x6e\40\x73\x74\x79\x6c\x65\x3d\x22\x63\157\x6c\157\x72\x3a\x72\145\144\73\x22\x3e\x2a\74\x2f\x73\160\141\156\x3e\72\x3c\x2f\x73\164\162\x6f\x6e\x67\76\x3c\x2f\x74\x64\76\15\12\x9\11\11\x9\x3c\x74\144\76";
    if ($xi and is_array($xi)) {
        goto gI;
    }
    echo "\74\151\156\160\x75\x74\x20\x74\171\x70\x65\x3d\42\164\145\170\164\42\x20\x6e\141\x6d\x65\75\42\163\141\155\x6c\x5f\x61\155\137\x65\x6d\141\151\x6c\x22\40\x70\x6c\141\x63\145\x68\157\x6c\x64\x65\162\75\42\x45\156\x74\x65\162\40\x61\x74\x74\x72\x69\x62\165\x74\145\40\156\141\x6d\x65\x20\146\157\x72\x20\105\155\141\x69\154\x22\x20\x73\x74\171\154\145\x3d\42\167\x69\x64\x74\x68\72\141\165\x74\157\73\40\155\x69\x6e\55\167\x69\x64\x74\150\x3a\65\x30\x25\73\x20\x6d\141\170\x2d\x77\x69\x64\x74\150\72\70\60\45\42\40\166\x61\x6c\165\x65\x3d\42" . $W_ . "\x22\x20\162\x65\x71\x75\x69\162\x65\x64";
    if (mo_saml_is_customer_license_key_verified()) {
        goto Zj;
    }
    echo "\x64\x69\163\x61\x62\x6c\x65\144";
    Zj:
    echo "\x2f\76";
    goto u8;
    gI:
    echo "\74\x73\x65\154\x65\x63\x74\40\156\141\x6d\145\75\x22\163\x61\155\x6c\137\141\x6d\x5f\145\x6d\141\x69\x6c\42\x20\163\164\171\x6c\145\x3d\42\x77\151\144\x74\x68\x3a\141\x75\164\x6f\x3b\x20\x6d\x69\156\x2d\x77\x69\144\x74\x68\x3a\x35\x30\45\73\x20\155\x61\x78\55\167\151\144\x74\x68\x3a\x38\60\45\x22";
    if (mo_saml_is_customer_license_key_verified()) {
        goto Py;
    }
    echo "\144\x69\x73\x61\142\154\x65\144";
    Py:
    echo "\x3e\xd\xa\x9\11\x9\11\x9\74\157\160\164\151\x6f\x6e\40\x76\141\x6c\165\x65\x3d\42\x22\76\x2d\55\x53\145\154\x65\143\164\x20\x61\x6e\40\101\x74\x74\x72\151\142\x75\164\x65\x2d\x2d\74\x2f\x6f\x70\x74\151\x6f\x6e\76";
    foreach ($xi as $ld => $g2) {
        $rr = $W_ == $ld ? "\x73\x65\x6c\145\143\x74\145\144" : '';
        echo "\74\x6f\x70\x74\x69\x6f\156\40\x76\141\154\x75\145\x3d\42" . $ld . "\x22\x20" . $rr . "\x20\x3e" . $ld . "\74\x2f\x6f\160\x74\151\x6f\156\x3e";
        de:
    }
    ew:
    echo "\x3c\57\x73\x65\154\x65\143\x74\76";
    u8:
    echo "\x3c\57\x74\x64\76\xd\12\x9\x9\11\x20\40\74\x2f\164\162\x3e\15\12\x9\x9\11\40\x20\74\164\162\x3e\15\12\x9\x9\11\11\x3c\164\x64\x3e\x3c\x73\164\162\157\156\x67\x3e\106\151\162\163\x74\x20\x4e\x61\155\145\x3a\x3c\x2f\163\164\162\157\156\147\x3e\x3c\x2f\164\x64\76\15\12\11\x9\11\11\74\x74\144\x3e";
    if ($xi and is_array($xi)) {
        goto bw;
    }
    echo "\74\x69\x6e\160\x75\164\x20\164\171\x70\145\75\42\164\145\170\x74\42\40\156\x61\155\145\x3d\x22\x73\141\155\154\137\141\155\x5f\x66\x69\x72\x73\x74\137\x6e\x61\x6d\145\x22\x20\x70\154\x61\143\x65\150\x6f\x6c\144\x65\x72\75\x22\x45\x6e\164\145\162\x20\141\164\x74\x72\151\142\165\164\145\x20\156\141\155\x65\x20\146\x6f\162\x20\106\151\162\x73\164\40\116\x61\x6d\x65\x22\40\x73\164\x79\x6c\145\75\x22\x77\x69\144\164\150\72\x61\165\x74\x6f\73\x20\155\x69\x6e\x2d\167\151\x64\x74\150\72\65\60\45\x3b\x20\x6d\141\170\x2d\x77\151\x64\x74\x68\x3a\x38\x30\45\42\x20\166\141\x6c\x75\145\75\42" . $k1 . "\x22";
    if (mo_saml_is_customer_license_key_verified()) {
        goto Bw;
    }
    echo "\x64\x69\x73\141\142\154\x65\x64";
    Bw:
    echo "\57\76";
    goto dc;
    bw:
    echo "\74\x73\x65\154\x65\x63\164\40\x6e\x61\155\x65\x3d\x22\163\x61\x6d\154\x5f\x61\155\137\x66\151\x72\x73\164\137\156\141\155\145\42\40\163\x74\x79\x6c\145\x3d\x22\x77\x69\x64\164\150\x3a\x61\x75\x74\x6f\x3b\40\155\x69\x6e\x2d\167\x69\x64\164\x68\x3a\65\60\x25\x3b\x20\x6d\x61\x78\55\167\151\144\x74\x68\72\70\x30\45\x22";
    if (mo_saml_is_customer_license_key_verified()) {
        goto Ms;
    }
    echo "\144\x69\x73\x61\142\x6c\x65\144";
    Ms:
    echo "\x3e\15\12\x9\x9\11\x9\x9\74\x6f\160\x74\x69\x6f\156\x20\x76\141\x6c\165\145\x3d\x22\42\x3e\55\x2d\123\145\154\145\x63\164\x20\x61\156\x20\x41\x74\x74\162\151\142\165\164\145\x2d\55\74\57\157\160\x74\x69\157\x6e\x3e";
    foreach ($xi as $ld => $g2) {
        $rr = $k1 == $ld ? "\x73\145\x6c\x65\x63\164\x65\144" : '';
        echo "\x3c\157\160\x74\x69\x6f\x6e\40\x76\x61\x6c\x75\145\75\42" . $ld . "\42\x20" . $rr . "\x20\76" . $ld . "\x3c\57\157\160\164\x69\x6f\x6e\76";
        Dc:
    }
    oo:
    echo "\x3c\57\163\145\x6c\x65\x63\x74\76";
    dc:
    echo "\x3c\57\x74\144\x3e\xd\xa\x9\11\11\x20\x20\74\57\164\x72\x3e\xd\xa\11\x9\x9\40\40\x3c\x74\162\76\15\12\11\11\x9\11\74\x74\x64\x3e\x3c\163\164\x72\x6f\156\147\x3e\x4c\x61\x73\164\40\116\x61\x6d\x65\72\74\57\x73\164\162\x6f\x6e\x67\76\74\57\164\144\76\xd\12\x9\11\11\11\74\164\x64\x3e";
    if ($xi and is_array($xi)) {
        goto ak;
    }
    echo "\74\151\x6e\160\165\x74\40\x74\x79\x70\145\x3d\x22\x74\x65\x78\164\x22\40\156\141\x6d\x65\75\42\163\141\155\x6c\137\x61\155\137\x6c\141\x73\x74\x5f\156\141\155\x65\x22\x20\x70\154\x61\143\145\x68\x6f\154\x64\145\162\x3d\x22\105\156\x74\x65\x72\x20\x61\164\x74\162\151\142\165\164\145\x20\x6e\141\x6d\145\x20\146\x6f\162\40\x4c\141\163\x74\40\116\x61\x6d\x65\x22\x20\163\164\171\154\145\x3d\x22\x77\151\x64\x74\x68\72\141\165\x74\x6f\x3b\x20\x6d\151\x6e\x2d\167\151\144\164\150\72\65\x30\45\x3b\40\x6d\141\x78\x2d\167\151\144\x74\x68\x3a\70\x30\45\42\x20\166\141\x6c\165\145\x3d\x22" . $Wj . "\x22";
    if (mo_saml_is_customer_registered_saml()) {
        goto C9;
    }
    echo "\x64\151\163\x61\x62\x6c\x65\144";
    C9:
    echo "\57\x3e";
    goto yQ;
    ak:
    echo "\74\163\x65\x6c\145\143\x74\x20\156\x61\x6d\x65\x3d\x22\x73\141\155\154\x5f\141\x6d\x5f\x6c\x61\x73\164\x5f\156\141\x6d\145\x22\40\x73\164\x79\x6c\145\75\42\x77\x69\144\164\150\x3a\x61\165\164\x6f\x3b\x20\155\x69\156\55\x77\151\x64\x74\150\72\65\x30\45\x3b\40\x6d\x61\x78\55\x77\151\144\164\x68\72\x38\60\45\42";
    if (mo_saml_is_customer_license_key_verified()) {
        goto d2;
    }
    echo "\144\151\x73\x61\142\x6c\x65\x64";
    d2:
    echo "\76\15\12\x9\x9\11\11\11\74\157\160\164\x69\157\x6e\40\166\x61\154\x75\x65\75\42\42\76\55\55\123\x65\154\x65\143\164\40\x61\156\x20\x41\x74\x74\x72\x69\142\x75\x74\x65\x2d\55\74\57\x6f\160\x74\151\157\156\x3e";
    foreach ($xi as $ld => $g2) {
        $rr = $Wj == $ld ? "\163\145\x6c\x65\143\164\x65\x64" : '';
        echo "\x3c\x6f\160\164\x69\157\x6e\x20\166\141\154\x75\x65\75\x22" . $ld . "\x22\x20" . $rr . "\x20\x3e" . $ld . "\x3c\x2f\x6f\x70\164\151\157\x6e\x3e";
        Gn:
    }
    dD:
    echo "\74\57\163\x65\154\x65\143\164\76";
    yQ:
    echo "\x3c\x2f\x74\144\76\xd\12\11\x9\x9\40\40\74\x2f\164\162\x3e\15\xa\11\x9\x9\40\x20\74\x74\x72\76\xd\xa\x9\11\x9\x9\74\x74\144\76\74\x73\164\162\157\x6e\147\x3e\x44\151\x73\x70\x6c\x61\x79\40\116\141\155\145\72\x3c\57\x73\164\162\x6f\156\147\76\74\x2f\164\144\76\15\12\11\11\11\x9\x3c\164\x64\x3e\15\xa\11\x9\11\11\11\x3c\x73\145\x6c\x65\x63\164\40\163\164\171\154\x65\75\42\x77\x69\x64\164\x68\72\x61\x75\164\157\73\x20\155\x69\x6e\55\167\x69\144\164\x68\72\x35\60\45\x3b\x20\x6d\141\x78\55\x77\151\144\164\150\x3a\70\60\45\42\40\x6e\141\155\145\75\x22\163\141\155\x6c\137\141\155\x5f\x64\x69\163\x70\x6c\141\x79\x5f\x6e\x61\155\x65\42\x20\x69\144\75\x22\163\141\x6d\x6c\137\x61\x6d\x5f\144\151\163\160\154\141\171\137\156\141\x6d\145\x22";
    if (mo_saml_is_customer_license_key_verified()) {
        goto LP;
    }
    echo "\x64\x69\163\x61\x62\154\x65\x64";
    LP:
    echo "\x3e\xd\12\11\x9\11\x9\11\11\x3c\x6f\160\x74\x69\x6f\156\x20\166\141\x6c\x75\145\x3d\x22\x55\123\105\122\116\101\115\105\42";
    if (!(get_option("\163\x61\x6d\154\x5f\141\155\137\x64\x69\163\160\154\x61\x79\137\x6e\141\x6d\x65") == "\x55\123\x45\122\x4e\101\115\105")) {
        goto M5;
    }
    echo "\x73\x65\154\x65\x63\x74\x65\x64\75\x22\x73\145\154\145\143\164\x65\x64\42";
    M5:
    echo "\76\125\x73\145\x72\x6e\141\155\145\74\x2f\x6f\x70\164\151\x6f\156\76\15\xa\x9\x9\x9\x9\x9\11\x3c\157\160\164\x69\x6f\156\40\x76\x61\154\x75\145\75\x22\x46\116\x41\115\x45\42";
    if (!(get_option("\x73\141\x6d\x6c\137\x61\155\137\144\x69\x73\160\x6c\x61\171\137\x6e\x61\x6d\145") == "\x46\x4e\101\115\x45")) {
        goto ml;
    }
    echo "\x73\145\x6c\145\x63\x74\x65\144\x3d\42\163\x65\154\145\x63\164\145\x64\x22";
    ml:
    echo "\76\x46\x69\162\x73\x74\x4e\141\155\x65\74\57\x6f\x70\164\151\157\156\x3e\15\xa\11\x9\x9\11\x9\11\x3c\157\x70\164\x69\157\x6e\x20\x76\141\x6c\x75\145\75\x22\114\116\x41\115\x45\42";
    if (!(get_option("\163\141\x6d\x6c\x5f\141\x6d\x5f\144\151\163\x70\154\141\171\x5f\x6e\141\x6d\145") == "\114\x4e\x41\115\105")) {
        goto Az;
    }
    echo "\163\x65\154\145\143\x74\x65\144\x3d\42\x73\145\154\x65\x63\x74\145\x64\x22";
    Az:
    echo "\76\114\x61\163\x74\116\x61\x6d\145\74\x2f\x6f\160\164\x69\x6f\x6e\x3e\xd\xa\11\11\x9\x9\x9\11\x3c\157\x70\164\x69\x6f\x6e\x20\166\x61\154\165\145\75\x22\x46\116\x41\115\105\137\114\x4e\101\x4d\105\42";
    if (!(get_option("\x73\141\x6d\154\x5f\x61\155\x5f\x64\151\163\160\x6c\x61\x79\x5f\156\x61\x6d\145") == "\x46\x4e\101\115\105\137\x4c\116\x41\115\105")) {
        goto nk;
    }
    echo "\x73\x65\154\145\x63\x74\145\144\75\x22\x73\145\x6c\145\143\x74\145\144\x22";
    nk:
    echo "\76\x46\x69\x72\163\x74\116\141\155\x65\x20\114\141\163\x74\x4e\x61\x6d\145\x3c\57\x6f\x70\x74\x69\157\156\x3e\15\12\11\11\x9\11\x9\x9\x3c\157\x70\x74\x69\x6f\156\x20\166\141\154\165\x65\75\x22\x4c\x4e\101\115\x45\x5f\x46\x4e\101\115\105\x22";
    if (!(get_option("\163\x61\155\154\x5f\x61\155\x5f\144\151\163\160\154\141\x79\137\156\x61\x6d\145") == "\x4c\x4e\101\115\x45\137\106\x4e\x41\x4d\x45")) {
        goto pM;
    }
    echo "\163\x65\x6c\x65\143\x74\145\144\x3d\x22\x73\145\154\145\143\164\x65\x64\42";
    pM:
    echo "\76\x4c\x61\163\x74\x4e\141\x6d\x65\40\106\151\162\x73\x74\x4e\x61\155\x65\x3c\x2f\157\160\164\x69\x6f\x6e\x3e\15\12\x9\x9\11\11\11\74\57\x73\145\154\x65\143\164\x3e\15\12\x9\x9\x9\x9\74\x2f\164\144\x3e\xd\12\x9\11\11\40\40\74\x2f\x74\x72\76\xd\xa\x9\11\x9";
    echo "\74\164\x72\40\151\144\75\42\x73\141\x76\x65\x5f\143\157\156\x66\x69\x67\137\x65\154\145\x6d\x65\156\164\x22\x3e\xd\12\x9\x9\11\11\x3c\164\144\x20\x63\157\x6c\x73\x70\141\156\75\42\x32\x22\x3e\74\142\162\57\76\x3c\142\x72\x2f\76\x3c\144\x69\x76\x20\163\x74\x79\154\x65\75\x22\164\x65\x78\164\55\141\154\151\147\156\x3a\x20\x63\145\156\164\x65\x72\x22\76\x3c\142\162\x20\x2f\76\74\151\x6e\160\165\x74\40\164\171\x70\145\x3d\42\163\x75\x62\x6d\151\164\42\x20\x73\x74\x79\x6c\x65\x3d\42\x77\x69\144\x74\x68\x3a\x31\x30\60\160\x78\x3b\x22\40\x6e\x61\155\x65\x3d\42\163\165\142\155\x69\164\x22\40\x76\141\154\165\145\75\42\123\x61\166\x65\42\40\x63\x6c\x61\163\x73\75\42\x62\x75\164\x74\157\156\x20\x62\x75\x74\164\157\x6e\55\160\x72\x69\x6d\141\x72\171\x20\x62\165\x74\x74\x6f\x6e\55\x6c\141\x72\x67\x65\42";
    if (mo_saml_is_customer_license_key_verified()) {
        goto f7;
    }
    echo "\x64\151\163\x61\x62\154\x65\x64";
    f7:
    echo "\x2f\76\x3c\x2f\144\151\166\76\15\12\x9\x9\11\x9\74\57\x74\144\76\15\12\x9\11\x9\x20\40\74\x2f\x74\162\x3e\xd\12\x9\11\x9\40\74\57\x74\x61\142\x6c\145\76\74\x2f\144\151\166\76\15\12\11\11\11\40\74\x2f\146\157\162\x6d\76\xd\xa\11\x9\x9\40\74\142\x72\x20\x2f\76\xd\xa\x9\11\11\x20\74\x66\157\x72\155\x20\x6e\x61\155\145\x3d\x22\163\141\x6d\154\x5f\146\x6f\x72\155\137\141\x6d\x5f\162\x6f\x6c\x65\137\x6d\141\x70\x70\x69\156\147\x22\x20\x77\x69\x64\x74\150\x3d\x22\71\x38\45\42\x20\x62\157\x72\144\145\x72\75\42\x30\42\40\163\x74\171\154\x65\x3d\42\142\x61\143\153\147\x72\157\x75\156\x64\55\x63\157\x6c\x6f\162\x3a\x23\106\x46\106\106\106\x46\73\40\142\x6f\162\x64\145\162\72\x31\160\x78\x20\163\157\x6c\151\x64\40\43\103\103\103\x43\x43\x43\x3b\x20\x70\141\x64\x64\x69\x6e\147\x3a\x30\x70\170\x20\x30\160\x78\x20\60\160\x78\x20\x31\60\160\x78\73\x22\40\155\x65\164\150\x6f\x64\75\42\160\x6f\x73\164\42\x20\141\x63\x74\151\x6f\156\75\42\x22\76";
    wp_nonce_field("\154\157\147\x69\x6e\x5f\167\151\x64\147\145\x74\x5f\x73\x61\x6d\154\x5f\x72\x6f\154\145\137\x6d\x61\x70\160\151\x6e\x67");
    echo "\74\151\156\x70\165\x74\x20\164\x79\x70\x65\75\42\150\x69\x64\144\x65\156\42\x20\156\141\x6d\x65\75\42\x6f\160\164\x69\157\x6e\42\40\x76\141\x6c\165\145\x3d\42\x6c\157\147\151\156\x5f\167\151\x64\x67\145\164\137\x73\141\x6d\x6c\137\162\157\154\145\137\155\141\x70\x70\151\156\147\x22\x20\x2f\x3e\xd\12\x9\x9\11\11\x3c\150\x33\76\122\157\154\x65\x20\x4d\x61\x70\x70\151\x6e\147\40\50\117\x70\x74\151\157\156\141\x6c\x29\74\57\150\x33\x3e\x3c\150\162\76\xd\12\11\x9\x9\11\74\144\x69\166\x20\163\164\171\x6c\145\x3d\x22\x6d\x61\162\147\151\156\x3a\62\45\x20\60\40\62\45\40\61\60\160\170\73\x22\x3e\15\xa\11\x9\x9\11\x3c\x74\x61\142\x6c\145\40\x3e\xd\12\x9\x9\x9\x9\x9\x3c\x74\162\76\15\12\11\11\11\11\11\x9\74\164\x64\x20\x63\157\x6c\x73\160\x61\156\75\x22\x32\x22\x3e\xd\xa\xd\xa\x9\11\11\11\11\11\x3c\x2f\164\144\x3e\15\xa\11\x9\11\x9\11\x3c\x2f\164\x72\76\xd\12\x9\x9\11\11\11\x20\74\x74\x72\x3e\15\xa\x9\11\x9\x9\11\x20\x20\x9\74\164\x64\x20\x63\157\154\x73\160\141\x6e\x3d\x22\62\x22\76\x5b\x20\x3c\x61\x20\x74\x61\162\147\x65\164\x3d\42\137\x62\154\141\156\x6b\x22\x20\x68\162\x65\x66\x3d\42\150\164\x74\x70\x73\x3a\x2f\57\144\x6f\x63\163\x2e\x6d\x69\x6e\151\157\x72\141\x6e\x67\x65\x2e\143\x6f\155\57\144\157\x63\x75\x6d\x65\156\x74\141\x74\151\157\156\57\163\x61\155\x6c\x2d\150\x61\x6e\144\x62\x6f\x6f\153\x2f\x61\164\x74\x72\151\x62\x75\x74\145\x2d\x72\157\154\145\55\x6d\x61\x70\x70\151\x6e\x67\57\x72\157\154\x65\55\155\141\x70\160\151\x6e\x67\x22\x3e\x43\154\151\x63\153\x20\110\145\162\145\x3c\57\x61\76\x20\x74\157\40\x6b\156\x6f\x77\40\x6d\157\x72\145\x20\141\x62\x6f\165\164\x20\164\150\151\x73\x20\146\145\x61\x74\x75\x72\145\x2e\40\x5d\xd\12\11\x9\x9\x9\11\x9\11\x3c\x2f\x74\144\x3e\xd\xa\x9\11\x9\x9\x9\x20\x20\x3c\57\164\162\76\xd\xa\x9\11\x9\x9\x9\74\164\162\76\x3c\164\144\40\x63\x6f\x6c\163\160\141\x6e\75\42\x32\x22\76\x3c\x62\x72\x2f\76\x3c\x62\x3e\116\117\x54\105\72\x20\x3c\x2f\x62\76\122\157\154\x65\40\167\151\x6c\x6c\x20\x62\x65\40\x61\163\x73\x69\x67\x6e\145\144\40\157\x6e\x6c\x79\40\x74\157\x20\x6e\x6f\x6e\55\141\x64\155\x69\x6e\x20\165\163\x65\x72\x73\x20\50\165\x73\x65\162\x20\x74\x68\x61\164\40\144\x6f\40\116\x4f\x54\x20\x68\141\x76\x65\x20\x41\144\155\151\x6e\151\163\164\x72\x61\x74\157\162\x20\160\162\151\x76\x69\154\145\147\145\163\x29\56\x20\x59\157\x75\40\x77\151\154\154\40\150\x61\x76\x65\40\164\157\40\155\141\156\x75\141\154\154\171\x20\x63\150\x61\156\x67\145\x20\x74\150\x65\x20\162\x6f\x6c\x65\x20\157\146\40\101\x64\155\x69\156\151\163\164\162\x61\x74\x6f\x72\x20\165\163\145\x72\x73\x2e\74\x62\x72\x20\57\x3e\74\142\162\x2f\76\74\x2f\164\x64\76\x3c\x2f\x74\x72\x3e\15\12\11\11\11\11\x9\74\x74\x72\76\x3c\164\144\40\x63\x6f\x6c\x73\x70\x61\x6e\x3d\42\62\x22\x3e";
    $Cb = get_option("\x73\x61\155\x6c\x5f\x61\x6d\137\144\157\156\x74\x5f\165\160\144\141\164\x65\x5f\x65\170\151\x73\164\x69\156\x67\137\165\x73\x65\x72\x5f\x72\x6f\x6c\145");
    if (!empty($Cb)) {
        goto yW;
    }
    $Cb = "\143\150\x65\x63\x6b\145\144";
    yW:
    echo "\74\154\x61\142\x65\x6c\40\x63\x6c\x61\163\163\x3d\x22\163\x77\151\x74\143\x68\42\x3e\xd\12\11\x9\11\11\11\74\x69\x6e\160\x75\x74\x20\164\x79\x70\145\75\42\x63\x68\x65\143\153\142\157\170\42\x20\151\x64\75\42\144\157\x6e\x74\x5f\165\x70\x64\141\x74\145\137\145\x78\151\163\164\151\x6e\x67\x5f\x75\x73\x65\162\137\x72\x6f\x6c\x65\42\40\x6e\x61\155\x65\75\x22\x6d\157\x5f\x73\x61\155\154\137\x64\x6f\156\x74\137\165\x70\x64\x61\164\x65\x5f\145\x78\151\x73\x74\151\x6e\147\x5f\x75\x73\145\x72\137\x72\157\154\145\42\x20\166\x61\x6c\x75\145\75\42\143\150\x65\x63\153\x65\x64\42" . $Cb;
    if (mo_saml_is_customer_license_key_verified()) {
        goto xb;
    }
    echo "\40\144\x69\x73\x61\x62\x6c\x65\x64\40";
    xb:
    echo "\x2f\76\xd\12\x9\11\11\x9\x9\x3c\x73\160\141\156\x20\x63\154\141\163\x73\x3d\42\163\154\x69\144\x65\x72\x20\x72\x6f\165\x6e\x64\x22\76\74\57\163\x70\141\156\76\15\12\x9\11\x9\x9\11\x3c\57\x6c\x61\142\145\x6c\x3e\15\12\x9\11\11\11\x9\74\163\160\x61\156\x20\163\x74\x79\154\145\75\x22\x70\141\x64\144\151\x6e\x67\55\154\145\x66\x74\x3a\x35\160\x78\42\x3e\74\x62\x3e\x44\x6f\x20\x6e\157\164\40\x75\160\144\141\x74\145\x20\x65\170\x69\x73\x74\151\156\147\x20\x75\163\x65\162\47\163\40\x72\x6f\154\145\163\56\74\x2f\x62\x3e\x3c\57\x73\160\x61\156\x3e\74\x62\162\40\x2f\x3e\x3c\x62\162\40\57\x3e\74\57\164\x64\76\74\x2f\x74\x72\76\xd\12\11\11\x9\11\11\74\x74\x72\x3e\xd\xa\x9\11\x9\x9\x9\11\74\x74\x64\76\x3c\x73\x74\x72\x6f\156\x67\76\x44\145\x66\x61\165\x6c\x74\40\x52\157\x6c\x65\x3a\74\57\163\164\162\x6f\x6e\147\x3e\x3c\x2f\x74\144\76\xd\12\11\x9\x9\11\x9\11\x3c\164\144\x3e";
    echo "\74\163\x65\154\x65\143\164\x20\151\144\x3d\42\x73\x61\x6d\x6c\137\x61\x6d\137\144\145\x66\141\165\154\164\x5f\165\x73\145\162\x5f\x72\157\x6c\x65\x22\x20\x6e\141\155\145\x3d\42\x73\x61\x6d\x6c\x5f\x61\155\137\x64\x65\146\141\165\154\164\137\x75\x73\145\x72\x5f\162\157\154\x65\42\40\163\164\171\154\x65\x3d\42\167\x69\144\164\x68\x3a\x31\65\60\160\170\x3b\42";
    if (mo_saml_is_customer_license_key_verified()) {
        goto Vj;
    }
    echo "\144\x69\163\x61\142\x6c\145\x64";
    Vj:
    echo "\76";
    $Am = get_option("\x73\141\x6d\154\137\x61\x6d\137\x64\x65\146\141\165\154\x74\x5f\x75\163\x65\162\137\162\x6f\x6c\x65");
    if (!empty($Am)) {
        goto w5;
    }
    $Am = get_option("\144\x65\146\x61\165\x6c\x74\x5f\x72\157\154\x65");
    w5:
    echo wp_dropdown_roles($Am);
    echo "\x9\74\57\x73\145\154\145\143\164\76\15\12\11\11\11\11\x9\x9\x9\46\156\142\163\x70\73\46\x6e\x62\163\160\73\46\156\142\163\x70\73\x26\x6e\x62\x73\x70\73\x3c\151\76\x53\x65\x6c\x65\x63\x74\x20\x74\150\x65\x20\144\x65\x66\x61\x75\154\164\x20\x72\x6f\x6c\145\40\164\157\40\x61\x73\x73\151\147\x6e\40\x74\x6f\40\x55\163\x65\162\x73\x2e\74\x2f\x69\76\xd\xa\x9\11\11\11\x9\x9\74\57\x74\x64\x3e\15\12\x9\x9\11\11\x20\40\11\x3c\57\x74\x72\x3e";
    $Bd = '';
    if (mo_saml_is_customer_license_key_verified()) {
        goto nX;
    }
    $Bd = "\40\x64\x69\x73\x61\142\x6c\x65\x64\x20";
    nX:
    echo "\x3c\164\162\76\15\xa\11\11\11\x9\11\x9\xd\xa\11\x9\x9\11\11\x9\x3c\x74\x64\40\143\x6f\x6c\163\160\x61\x6e\75\42\62\x22\40\x73\164\171\154\145\x3d\x22\x74\145\170\164\55\141\x6c\x69\147\x6e\72\40\143\x65\x6e\x74\x65\x72\73\42\x3e\x3c\x62\x72\x20\x2f\x3e\74\151\x6e\x70\165\164\x20\164\x79\160\145\75\x22\x73\x75\x62\x6d\x69\164\x22\x20\x73\164\171\x6c\145\x3d\x22\167\x69\144\x74\x68\x3a\61\x30\x30\160\x78\x3b\42\40\x6e\x61\x6d\x65\75\x22\x73\165\x62\x6d\151\x74\42\x20\166\141\154\x75\x65\75\42\x53\x61\x76\x65\42\x20\x63\154\141\x73\x73\75\x22\x62\165\x74\x74\157\156\40\142\x75\x74\x74\157\156\x2d\x70\162\x69\155\x61\x72\x79\40\x62\x75\x74\164\157\x6e\55\x6c\141\x72\147\x65\42";
    if (mo_saml_is_customer_license_key_verified()) {
        goto T3;
    }
    echo "\x64\x69\163\141\x62\154\145\x64";
    T3:
    echo "\x2f\x3e\40\46\156\142\163\x70\x3b\15\12\x9\x9\11\x9\x9\x9\74\x62\162\x20\57\76\74\142\x72\40\x2f\76\15\12\11\x9\11\11\x9\x9\x3c\57\x74\x64\76\15\xa\11\11\x9\11\x9\74\57\164\x72\76\xd\xa\11\11\x9\x9\x3c\x2f\x74\x61\142\154\145\x3e\74\x2f\144\x69\x76\76\15\xa\x9\11\11\74\57\x66\x6f\x72\155\76";
}
