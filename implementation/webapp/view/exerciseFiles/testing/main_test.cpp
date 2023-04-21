#include "gtest/gtest.h"
#include <string>

using std::string;

int main(int argc, char **argv) {
  ::testing::InitGoogleTest(&argc, argv);

  const int rv = RUN_ALL_TESTS();

  string testVector = "";

  //get test vector
  for (int i = 0; i < ::testing::UnitTest::GetInstance()->total_test_suite_count(); i++)
  {
    const ::testing::TestSuite* testSuite = ::testing::UnitTest::GetInstance()->GetTestSuite(i);

    for (int j = 0; j < testSuite->total_test_count(); j++)
    {
      const ::testing::TestInfo* testInfo = testSuite->GetTestInfo(j);

      if (testInfo->result()->Passed())
      {
        testVector = testVector + "1";
      }
      else
      {
        testVector = testVector + "0";
      }
    }
  }

  std::cout << "TEST_VECTOR:" << std::endl;
  std::cout << testVector << std::endl;
  std::cout << "DEBUGGING_TOOL_RESULT:" << std::endl;
  std::cout << ::testing::UnitTest::GetInstance()->successful_test_count() << std::endl;

  return rv;
}