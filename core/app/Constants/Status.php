<?php

namespace App\Constants;

class Status
{

	const ENABLE = 1;
	const DISABLE = 0;

	const YES = 1;
	const NO = 0;

	const PAYE = 1;
	const IMPAYE = 0;


	const VERIFIED = 1;
	const UNVERIFIED = 0;

	const TICKET_OPEN = 0;
	const TICKET_ANSWER = 1;
	const TICKET_REPLY = 2;
	const TICKET_CLOSE = 3;

	const PRIORITY_LOW = 1;
	const PRIORITY_MEDIUM = 2;
	const PRIORITY_HIGH = 3;

	const USER_ACTIVE = 1;
	const USER_BAN = 0;

	const ACTIVE_USER = 1;
	const BAN_USER = 0;

    const SUPER_ADMIN_ID=1;

    const COURIER_QUEUE = 0;
	const COURIER_DISPATCH = 1;
	const COURIER_UPCOMING = 1;
	const COURIER_DELIVERYQUEUE = 2;
	const COURIER_DELIVERED = 3;
}
