#include "NoSevens.h"

void NoSevens::removeSevens(int filter, int a, int b)
{
	for (int i = a; i <= b; i++)
	{
		if (i % filter != 0)
		{
			string stringVer = std::to_string(i);
			bool includesSevens = true;

			for (int j = 0; j < stringVer.size(); j++)
			{
				if (stringVer[j] == std::to_string(filter)[0])
				{
					includesSevens = false;
					break;
				}
			}

			if (!includesSevens)
			{
				cout << i << endl;
			}
		}
	}
}