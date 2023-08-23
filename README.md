# In development - de.xblackeye.wcf.payment.moneroLight
## Good to know
https://docs.woltlab.com/5.5/

Download: https://www.woltlab.com/en/woltlab-suite-download/

GitHub: https://github.com/WoltLab/WCF

These options are used exclusively for development and debugging, they’re not suitable for use in production environments.
Configuration -> Module -> Development


## Database
`wcf1_paid_subscription_transaction_log`

"logTime"
int(10)

"transactionID"
varchar(255)

"transactionDetails"
mediumtext

"logMessage"
varchar(255)


`wcf1_paid_subscription`

"cost"
decimal(10,2)

"currency"
varchar(3)


`wcf1_option`

"optionName" -> coingecko_fiat_to_xmr

"optionValue" -> Fiat to XMR Values (cronjob)
mediumtext

"optionName" -> coingecko_last_time

"optionValue" -> Last check (cronjob)
integer
