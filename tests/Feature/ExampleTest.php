<?php

test('halaman utama mengarahkan tamu ke halaman login', function () {
    $this->get('/')->assertRedirect('/dashboard');

    $this->get('/dashboard')->assertRedirect('/login');
});
