<?php
return [
    'api_key' => getenv('OPENAI_API_KEY') ?: '',
    'model' => getenv('OPENAI_MODEL') ?: 'gpt-3.5-turbo',
    'base_url' => getenv('OPENAI_BASE_URL') ?: 'https://api.openai.com/v1'
];
