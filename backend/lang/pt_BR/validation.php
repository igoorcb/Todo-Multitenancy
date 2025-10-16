<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'unique' => 'O :attribute já está em uso.',
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'max' => [
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
    ],
    'confirmed' => 'A confirmação de :attribute não confere.',
    'string' => 'O campo :attribute deve ser uma string.',
    'date' => 'O campo :attribute não é uma data válida.',
    'after_or_equal' => 'O campo :attribute deve ser uma data igual ou posterior a :date.',
    'in' => 'O :attribute selecionado é inválido.',
    'alpha_dash' => 'O campo :attribute pode conter apenas letras, números, traços e underscores.',

    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'password' => 'senha',
        'tenant_id' => 'empresa',
        'title' => 'título',
        'description' => 'descrição',
        'status' => 'status',
        'priority' => 'prioridade',
        'due_date' => 'data de vencimento',
        'slug' => 'identificador',
    ],
];
