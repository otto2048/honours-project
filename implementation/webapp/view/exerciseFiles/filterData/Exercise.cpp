#include "Exercise.h"

//https://www.learneroo.com/modules/20/nodes/157
void FilterData::filterData(int filter, int a, int b)
{
    for (int i = a; i <= b; i++)
	{
		if (i % filter == 0)
		{
			string stringVer = to_string(i);
			bool hasFilter = true;

			for (int j = 0; j < stringVer.size(); j++)
			{
				if (stringVer[j] == to_string(filter)[0])
				{
					hasFilter = false;
					break;
				}
			}

			if (!hasFilter)
			{
				cout << i << endl;
			}
		}
	}
}