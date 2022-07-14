<?php

declare(strict_types = 1);

if (php_sapi_name() != "cli") {
  throw new Exception("Sorry. Only for CLI ¯\_(ツ)_/¯", 1);
}

require_once './utils.php';

const MAX_REWARDS_OFFSET = 20;

main();

function main() {
  showMessage("Hello! The script calculates Solana stake reward for address\n");

  while (true) {
    $stakeAddress = readline("Enter your stake account address: ");
    if (isStakeAddressValid($stakeAddress)) break;

    showMessage("Address is invalid. Please, enter corrected account address");
  }

  showMessage("Getting stake account rewards...");
  $rewards = [];
  for ($i = 0; $i < MAX_REWARDS_OFFSET; $i++) {
    $fetchedRewards = fetchRewards(
      stakeAddress: $stakeAddress,
      offset: $i,
    );
    if (count($fetchedRewards) == 0) break;
    $rewards = array_merge($rewards, $fetchedRewards);
    sleep(1);
  }

  showMessage("Calculating rewards...");
  $solUsdPrice = getSolUsdPrice();

  $totalRewardsSolSum = array_sum(array_column($rewards, 'amount'));
  $totalRewardsSolSum = tokensToSol($totalRewardsSolSum);
  $totalRewardsUsdSum = $totalRewardsSolSum * $solUsdPrice;

  showMessage("\n-----------------\n");
  showMessage("You've earned " . formatSol($totalRewardsSolSum));
  showMessage("This equals " . formatUsd($totalRewardsUsdSum));
  showMessage("Current SOL price: " . formatUsd($solUsdPrice));
  showMessage("\nHave a nice day!  " . getRandomBenevolentEmoji());
}

