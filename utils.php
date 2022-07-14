<?php

define('SOL_TOKENS_DELIMITER', pow(10, 9));

function fetchRewards(string $stakeAddress, int $offset, int $limit = 10): array {
  $query = http_build_query([
    'address' => $stakeAddress,
    'offset' => $offset,
    'limit' => $limit,
  ]);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.solscan.io/validator/stake/reward?' . $query);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);

  if (curl_errno($ch)) {
    throw new Exception("Fetch rewards CURL error: " . curl_error($ch), 1);
  }

  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if ($httpCode != 200) {
    throw new Exception("Fetch rewards request error. Response HTTP code: $httpCode", 1);
  }

  $responseBody = json_decode($response, true);
  if ($responseBody['success'] === true) {
    return $responseBody['data'];
  }

  return [];
}

function showMessage(string $text) {
  print("$text\n");
}

function tokensToSol(float $value): float {
  return $value / SOL_TOKENS_DELIMITER;
}

function getSolUsdPrice(): float {
  $query = http_build_query([
    'ids' => 'solana',
    'vs_currencies' => 'usd',
  ]);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.coingecko.com/api/v3/simple/price?' . $query);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);

  if (curl_errno($ch)) {
    throw new Exception("Getting SOL price CURL error: " . curl_error($ch), 1);
  }

  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if ($httpCode != 200) {
    throw new Exception("Getting SOL price request error. Response HTTP code: $httpCode", 1);
  }

  $responseBody = json_decode($response, true);

  return $responseBody['solana']['usd'];
}

const BENEVOLENT_EMOJIS = [
  '( ^..^)ﾉ', 'ᵔᴥᵔ', 'ヽ(^o^)丿',
  '(′ʘ⌄ʘ‵)', '( ^_^)／', '(^-^*)/',
  '(◍•ᴗ•◍)❤', '∠( ᐛ 」∠)＿', 'ᕕ( ᐛ )ᕗ',
];
function getRandomBenevolentEmoji(): string {
  return BENEVOLENT_EMOJIS[array_rand(BENEVOLENT_EMOJIS)];
}

function formatUsd(float $value): string {
  return '$' . number_format($value, 3, '.', ',');
}

function formatSol(float $value): string {
  return number_format($value, 6, '.', '') . 'SOL';
}

function isStakeAddressValid($address): bool {
  return strlen($address) === 44;
}
