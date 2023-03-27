#pragma once
#include <string>

using std::string;

class Vehicle
{
public:
	const static int numColours = 9;
	const static int numManufacturers = 3;

private:

	string colours[numColours] = { "Blue", "Red", "White", "Black", "Orange", "Yellow", "Pink", "Green", "Light Blue" };
	
	string manufacturers[numManufacturers] = { "test", "test1", "test2" };

protected:
	int id;
	int manufacturer;
	int colour;
	float mpg;
	float fuelCap;
	float price;
	int year;
	string reg;

	float range;

public:
	Vehicle();
	Vehicle(int, int, int, float, float, float, int, string);

	int getId();
	string getManufacturer();
	float getPrice();
	string getColourName();
	int getColour();
	float getMpg();
	float getRange();
	float getFuelCap();
	int getYear();
	string getReg();
};

