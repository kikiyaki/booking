##Booking api service
Built on Laravel

##Local deployment instruction
In project directory

Build containers:

``docker-compose up -d``

Get into application container:

``docker exec -it booking_app bash``

Migrating and seeding:

``php artisan migrate``

``php artisan db:seed``


##Request examples

###Get all rooms:

GET localhost:8087/api/rooms

###Create booking in room #1:
POST localhost:8087/api/bookings

Request:

``{
"room_id": 1,
"user_id": 1,
"starts_at": "2026-10-10 11:00:00",
"ends_at": "2026-10-10 11:40:00"
}``

###Get bookings by user:

GET localhost:8087/api/bookings?user_id=1

###Get bookings by room:

localhost:8087/api/bookings?room_id=1

###Try to overlay booking in room #1 with 409 error:

POST localhost:8087/api/bookings
``{
"room_id": 1,
"user_id": 2,
"starts_at": "2026-10-10 11:20:00",
"ends_at": "2026-10-10 11:30:00"
}``
