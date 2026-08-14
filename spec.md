<!-- Start with a basic spec idea -->

# Salon Booking System — SPEC

## Overview
A simple web app where salon customers book available time slots,
and the salon admin manages slots and views all bookings.

## Roles
- Customer — views open slots, books an appointment
- Admin — sets available slots, sees all bookings

## Features
Customer:
- View available time slots for a chosen service
- Book a slot by entering name + phone
- See confirmation after booking

Admin:
- Set available time slots (day + time)
- View all bookings in a list

## Data
- Services: name (e.g. Haircut, Hair Color)
- Slots: date, time, is_available
- Bookings: customer name, phone, slot, created_at

## Rules
- One booking per slot — once booked, no longer available
- A booking must have a name and phone
