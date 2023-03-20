#pragma once
#include <string>

using std::string;

class Vehicle
{
public:
	const static int numColours = 9;
	const static int numManufacturers = 3;

private:
	//colour options
	string colours[numColours] = { "Blue", "Red", "White", "Black", "Orange", "Yellow", "Pink", "Green", "Light Blue" };
	
	//manufacturer options
	string manufacturers[numManufacturers] = { "test", "test1", "test2" };

protected:
	int id;
	int manufacturer;
	int colour;
	float mpg;
	float fuelCap;
	float price;

	float range;

public:
	Vehicle();
	Vehicle(int, int, int, float, float, float);

	//getters
	int getId();
	string getManufacturer();
	float getPrice();
	string getColourName();
	int getColour();
	float getMpg();
	float getRange();
	float getFuelCap();
};

