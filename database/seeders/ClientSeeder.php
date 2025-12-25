<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Ahmed Al-Rashid',
                'company_name' => 'Al-Rashid Real Estate Development',
                'email' => 'ahmed@alrashid-dev.om',
                'phone' => '+968 9123 4567',
                'secondary_phone' => '+968 2412 3456',
                'address' => 'Building 45, Street 12, Al Khuwair',
                'city' => 'Muscat',
                'country' => 'Oman',
                'tax_number' => 'OM12345678',
                'website' => 'https://alrashid-dev.om',
                'notes' => 'Premium client - handles luxury residential projects',
                'status' => 'active',
            ],
            [
                'name' => 'Fatima Al-Balushi',
                'company_name' => 'Oman Hospitality Group',
                'email' => 'fatima@omanhospitality.om',
                'phone' => '+968 9234 5678',
                'secondary_phone' => null,
                'address' => 'Tower C, Oman Business Park',
                'city' => 'Muscat',
                'country' => 'Oman',
                'tax_number' => 'OM23456789',
                'website' => 'https://omanhospitality.om',
                'notes' => 'Hotel and resort interior design projects',
                'status' => 'active',
            ],
            [
                'name' => 'Khalid Al-Hinai',
                'company_name' => 'Gulf Commercial Properties',
                'email' => 'khalid@gulfcommercial.om',
                'phone' => '+968 9345 6789',
                'secondary_phone' => '+968 2456 7890',
                'address' => 'Office 201, Commercial District',
                'city' => 'Sohar',
                'country' => 'Oman',
                'tax_number' => 'OM34567890',
                'website' => 'https://gulfcommercial.om',
                'notes' => 'Commercial and office space developments',
                'status' => 'active',
            ],
            [
                'name' => 'Maryam Al-Lawati',
                'company_name' => 'Wellness Centers LLC',
                'email' => 'maryam@wellnesscenters.om',
                'phone' => '+968 9456 7890',
                'secondary_phone' => null,
                'address' => 'Villa 78, Shatti Al Qurum',
                'city' => 'Muscat',
                'country' => 'Oman',
                'tax_number' => 'OM45678901',
                'website' => 'https://wellnesscenters.om',
                'notes' => 'Spa and wellness facility designs',
                'status' => 'active',
            ],
            [
                'name' => 'Said Al-Farsi',
                'company_name' => 'Al-Farsi Restaurants Group',
                'email' => 'said@alfarsi-restaurants.om',
                'phone' => '+968 9567 8901',
                'secondary_phone' => '+968 2567 8901',
                'address' => 'Building 12, Ruwi High Street',
                'city' => 'Muscat',
                'country' => 'Oman',
                'tax_number' => 'OM56789012',
                'website' => 'https://alfarsi-restaurants.om',
                'notes' => 'Restaurant and café interior projects',
                'status' => 'active',
            ],
            [
                'name' => 'Noor Al-Kharusi',
                'company_name' => 'Educational Facilities Development',
                'email' => 'noor@edufacilities.om',
                'phone' => '+968 9678 9012',
                'secondary_phone' => null,
                'address' => 'Knowledge Park, Building A',
                'city' => 'Muscat',
                'country' => 'Oman',
                'tax_number' => 'OM67890123',
                'website' => 'https://edufacilities.om',
                'notes' => 'Schools and educational institution designs',
                'status' => 'active',
            ],
            [
                'name' => 'Hamad Al-Zadjali',
                'company_name' => 'Dhofar Industrial Holdings',
                'email' => 'hamad@dhofarindustrial.om',
                'phone' => '+968 9789 0123',
                'secondary_phone' => '+968 2367 8901',
                'address' => 'Industrial Area, Plot 45',
                'city' => 'Salalah',
                'country' => 'Oman',
                'tax_number' => 'OM78901234',
                'website' => 'https://dhofarindustrial.om',
                'notes' => 'Industrial and warehouse facility designs',
                'status' => 'active',
            ],
            [
                'name' => 'Layla Al-Siyabi',
                'company_name' => 'Luxury Villas Oman',
                'email' => 'layla@luxuryvillas.om',
                'phone' => '+968 9890 1234',
                'secondary_phone' => null,
                'address' => 'The Wave, Muscat',
                'city' => 'Muscat',
                'country' => 'Oman',
                'tax_number' => 'OM89012345',
                'website' => 'https://luxuryvillas.om',
                'notes' => 'High-end residential villa projects',
                'status' => 'active',
            ],
            [
                'name' => 'Omar Al-Busaidi',
                'company_name' => 'Healthcare Developments LLC',
                'email' => 'omar@healthcaredev.om',
                'phone' => '+968 9901 2345',
                'secondary_phone' => '+968 2478 9012',
                'address' => 'Medical City, Building 3',
                'city' => 'Muscat',
                'country' => 'Oman',
                'tax_number' => 'OM90123456',
                'website' => 'https://healthcaredev.om',
                'notes' => 'Clinic and hospital interior designs',
                'status' => 'active',
            ],
            [
                'name' => 'Salim Al-Harthi',
                'company_name' => 'Muscat Retail Holdings',
                'email' => 'salim@muscatretail.om',
                'phone' => '+968 9012 3456',
                'secondary_phone' => null,
                'address' => 'City Centre, Tower B',
                'city' => 'Muscat',
                'country' => 'Oman',
                'tax_number' => 'OM01234567',
                'website' => 'https://muscatretail.om',
                'notes' => 'Retail store and shopping mall designs',
                'status' => 'inactive',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
