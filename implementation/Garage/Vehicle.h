#pragma once
#include <string>

using std::string;

class Vehicle
{
private:
	string colours[9] = { "Blue", "Red", "White", "Black", "Orange", "Yellow", "Pink", "Green", "Light Blue" };
protected:
	int id;
	string regNum;
	string manufacturer;
	int colour;
	float mpg;
	float fuelCap;
	float price;

	float range;

public:
	Vehicle();
	Vehicle(int, string, string, int, float, float, float);

	int getId();
	string getRegNum();
	string getManufacturer();
	float getPrice();
	string getColour();
	float getMpg();
	float getRange();
	float getFuelCap();
};

