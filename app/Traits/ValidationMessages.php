<?php

namespace App\Traits;

trait ValidationMessages {
  protected function generateCustomMessage() {
    return [
      // 'array' => 'The :attribute must be an array.',
      'boolean' => 'harus true atau false',
      'email' => 'tidak valid',
      'image' => 'harus memiliki format gambar',
      'integer' => 'harus memiliki format angka',
      'required' => 'harus diisi',
      'required_without_all' => 'harus diisi',
      'string' => 'harus memiliki format teks'
    ];
  }
}