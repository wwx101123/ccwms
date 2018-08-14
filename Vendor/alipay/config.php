<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016031401208430",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEAnGHIfUGTCs27uZzRtaBcTGDrmbpiQIbm78+bb6LTcNhi9NVeYVH1NvmuSAy9Q+r6gNbNl8ZwliJncK7zhWi0anOKvPX06nnE/H85hZ+TK4muWXqn8qf5a4Eb+KQ/GBtp7OvlaCcIqxk/LaDVG5A8BegH0B2ylRQf0+66AZmKZwJ1qRi6t3DIHEK7Dg0QSm0KE2FB1o8ENPtyb0mvzFqPo/KTf0J2Uhe8/4CSW08NzB70SWr7Zz640wXMbRTM6AbTnKpQx1VUWPc+MnDWG50OdfiprkUQvF5E5p+P/P0B+FkfH+jw/uozd8D/eNr/7kGTq/i/FE/d6j+B0kHEClGoowIDAQABAoIBAE+HRv+0RoRbBlopz1iP2gCZ1ZdTez4XnQBeU00dwGwGD6U1kY7qsEsqBSDtIZ+Vs5msI9hI+d7QVkyvHHc/PWlZozff2ZMZJD3Isccr1RtyJHGq+Bhmpmiuuzviw5eN8XukqAEkwLrK1x9sSJSA8JWMX9TOFZDNMDaGlJ/VptYlVYlhaoTnJeCFzqh5PFJAYlemlDQJZu00snT1nxZceugwg1TJXg9nzWDfWCNO1Yw+bkat7WE6yJLFOwEbPsYXJLC8JSahJau1WFaL/loLmVY2rx5vXb/5SFDYKaAJ6oEMR1jC8wy+dnDslipdeIFewdCxj6UgJw8cjp2esyQnS0ECgYEAy2vcz3pgLoOP7j5BD7PWdAoHpaH1rvdaYgR5S+RNmSFdg3u90OVcfVv6jfY6vDqQTLUw7LhN4jRrJNLeLvcDDc7VPk5mii+v1xk1eGEg1w5zswoemoxlaBdYR6k+rfHXpQ2jOlqfoB9hJtGRDLZci2uouZOqgAhwPfHpyS5Ei6ECgYEAxM1iO4ST1tbwo2yg2h44uD9XI1UhiletpfAtuMIxvsORZwOc1beHsgTd7w1RBc14HVPvbaZn6xuyyE41PDyQ0Bb+vhFQVgiI4gS2oa6Ufj1vXp4zkGRw1mca5eqn04+c/q0C7bl8p31P5l1K/O2n4eOL2J/2g9rahRgmwRWQLcMCgYBuEJqVGF3aTQ0fl0eUu3WZq6OflxZb6TJaPply2sCxzj/O2LFHyhJVMeNL1KLLHdTd7FvgeYiKkFo4vm/BcZ2RZwwyfjE1K8A03kr0K3mdYxBvG1abwMNNovP9MjHb5DMN92cON+KOvOvQGSwmeLVyKnpMjv198RLqMW6RkBs+gQKBgHZqxrLdYT+icYVENb1I9gQXSM1fyiC+BSSV0k0bvGb2siT0DYijOCkruIbA0oHVUnMDMEwgFuNm9TqFpI6sOs6bJ4kiTd6WoBavk1zR+VPEj6C1PH2jkinQPGUfvwATp/muDNcSBymYw1zvwdFTBvNqwZkFF4XbSCVRlPAVk32VAoGBALWniA8MhiNBmks4la2OJtP0KK1kCNuzU4bAxXHvv8ALFQiVOLhIHI6D31eER7F8Nvu3YVe2Hb909Nql5crQmKS0Hp3y0VDqjtjpsfDeFqw+fCLdKwpdJOyXMwKyCTC2KYHlf8Mqa0A3znPUA6K9khLRvThmkkjdyx3bnUgNnf1K",
		
		//异步通知地址
		'notify_url' => "http://test.com",
		
		//同步跳转
		'return_url' => "http://test.com",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvlQZ4hGfw9iZxbYMkrCZl57ueOOtIFIglS4+He4hl9qaJ61S3/r4PdPlQIUx1kyGmy71H171cMEphG+mS/v7x8wdAIfrm50EBUExRH3h9sck5Bd1PgnQ4jeaSddKs0tOjIBAjcM3Q5xQn0QLU6nXpYbIHEl2UBFAsB7Wdejh2mPznh8IK33fN1MVmlKSY0XNUC38xQiGF9wdZ/Do7a1xJ3IKii+248KQPbD+t72/eqEdoQUiV5FvbCb7QVmvWR4lNscYqvRtHx2QYAsy4w0Rg/xpOE/tmsKf56xJa8YZ/zAzOd4qAycUYAm9LvIXs70OQ10oMpPCMD9OVD3xU12apwIDAQAB",
		
	
);