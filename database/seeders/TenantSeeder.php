<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionType;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo tenant
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'demo'],
            [
                'name'    => 'PTE Academy Demo',
                'email'   => 'admin@pteacademy.com',
                'domain'  => null,
                'country' => 'AU',
                'timezone'=> 'Australia/Sydney',
            ]
        );

        // Create tenant admin
        User::firstOrCreate(
            ['email' => 'admin@pteacademy.com', 'tenant_id' => $tenant->id],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
                'role'     => 'tenant_admin',
            ]
        );

        // Create demo student
        $student = User::firstOrCreate(
            ['email' => 'student@demo.com', 'tenant_id' => $tenant->id],
            [
                'name'     => 'Jane Student',
                'password' => Hash::make('password'),
                'role'     => 'student',
            ]
        );

        // Create super admin (no tenant)
        User::firstOrCreate(
            ['email' => 'super@pteportal.com', 'tenant_id' => null],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'role'      => 'super_admin',
                'tenant_id' => null,
            ]
        );

        // Create subscription plans
        $basicPlan = SubscriptionPlan::firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'basic'],
            [
                'name'        => 'Basic Plan',
                'description' => 'Access to all practice questions with basic feedback.',
                'price'       => 29.99,
                'currency'    => 'AUD',
                'interval'    => 'monthly',
                'trial_days'  => 7,
                'features'    => [
                    'All speaking practice questions',
                    'All reading practice questions',
                    'All writing practice questions',
                    'All listening practice questions',
                    'Instant automated scoring',
                    '7-day free trial',
                ],
                'max_attempts_per_day' => 20,
                'includes_mock_test'   => false,
                'includes_ai_feedback' => false,
            ]
        );

        $proPlan = SubscriptionPlan::firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'pro'],
            [
                'name'        => 'Pro Plan',
                'description' => 'Everything in Basic plus mock tests and AI feedback.',
                'price'       => 59.99,
                'currency'    => 'AUD',
                'interval'    => 'monthly',
                'trial_days'  => 7,
                'is_featured' => true,
                'features'    => [
                    'Everything in Basic',
                    'Full mock PTE exam',
                    'AI-powered detailed feedback',
                    'Score predictions',
                    'Unlimited daily attempts',
                    'Progress analytics',
                    'Priority support',
                ],
                'max_attempts_per_day' => 999,
                'includes_mock_test'   => true,
                'includes_ai_feedback' => true,
            ]
        );

        // Sample questions
        $this->seedSampleQuestions($tenant);
    }

    private function seedSampleQuestions(Tenant $tenant): void
    {
        $readAloud = QuestionType::where('slug', 'read-aloud')->first();
        $mcSingle  = QuestionType::where('slug', 'reading-multiple-choice-single')->first();
        $essay     = QuestionType::where('slug', 'write-essay')->first();
        $dictation = QuestionType::where('slug', 'write-from-dictation')->first();
        $fillBlanks= QuestionType::where('slug', 'reading-fill-blanks')->first();
        $reorder   = QuestionType::where('slug', 'reorder-paragraphs')->first();

        // Read Aloud sample
        Question::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Climate Change Overview'],
            [
                'question_type_id' => $readAloud->id,
                'content'          => 'Climate change refers to long-term shifts in temperatures and weather patterns. These shifts may be natural, but since the 1800s, human activities have been the main driver of climate change, primarily due to the burning of fossil fuels like coal, oil, and gas. Burning fossil fuels generates greenhouse gas emissions that act like a blanket wrapped around the Earth, trapping the sun\'s heat and raising temperatures.',
                'difficulty'       => 'medium',
            ]
        );

        // MCQ sample
        $q = Question::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Reading: The Internet Revolution'],
            [
                'question_type_id' => $mcSingle->id,
                'content'          => 'The internet has fundamentally transformed how humans communicate, access information, and conduct business. Within just three decades, it has grown from a niche academic tool to a global network connecting billions of people. This rapid expansion has brought tremendous benefits, including instant access to knowledge, new economic opportunities, and the ability to maintain relationships across vast distances. However, it has also introduced new challenges related to privacy, misinformation, and digital inequality.',
                'difficulty'       => 'medium',
            ]
        );

        QuestionOption::firstOrCreate(['question_id' => $q->id, 'label' => 'A'], [
            'content' => 'The internet has had both positive and negative impacts on society.', 'is_correct' => true, 'sort_order' => 0,
        ]);
        QuestionOption::firstOrCreate(['question_id' => $q->id, 'label' => 'B'], [
            'content' => 'The internet was initially developed for commercial purposes.', 'is_correct' => false, 'sort_order' => 1,
        ]);
        QuestionOption::firstOrCreate(['question_id' => $q->id, 'label' => 'C'], [
            'content' => 'Digital inequality is the most significant problem created by the internet.', 'is_correct' => false, 'sort_order' => 2,
        ]);
        QuestionOption::firstOrCreate(['question_id' => $q->id, 'label' => 'D'], [
            'content' => 'The internet has been in use for over a century.', 'is_correct' => false, 'sort_order' => 3,
        ]);

        // Essay sample
        Question::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Essay: Technology in Education'],
            [
                'question_type_id' => $essay->id,
                'content'          => 'In the modern world, technology plays an increasingly important role in education. Some people believe that technology enhances learning, while others think it creates distractions and reduces important skills. Discuss both views and give your own opinion.',
                'sample_answer'    => 'Technology has transformed education in profound ways. On one hand, digital tools provide students with instant access to vast amounts of information, enabling self-directed learning and personalized education. Interactive platforms make complex concepts more engaging through multimedia presentations. On the other hand, technology can create distractions, and students may become overly dependent on devices, potentially reducing critical thinking skills. Despite these challenges, I believe technology\'s benefits outweigh its drawbacks when used thoughtfully. The key lies in teachers guiding students to use technology as a tool for learning rather than entertainment.',
                'difficulty'       => 'medium',
            ]
        );

        // Dictation sample
        Question::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Dictation: Environmental Protection'],
            [
                'question_type_id' => $dictation->id,
                'content'          => 'Environmental protection requires both individual action and government policy.',
                'difficulty'       => 'easy',
            ]
        );

        // Fill in the blanks sample
        Question::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Fill Blanks: Water Cycle'],
            [
                'question_type_id' => $fillBlanks->id,
                'content'          => 'The water cycle, also known as the hydrological {1}, describes the continuous movement of water within Earth and its {2}. The sun drives this cycle by {3} water from the surface, which rises into the atmosphere and eventually {4} as rain or snow.',
                'blanks'           => [
                    ['id' => 1, 'answer' => 'cycle'],
                    ['id' => 2, 'answer' => 'atmosphere'],
                    ['id' => 3, 'answer' => 'evaporating'],
                    ['id' => 4, 'answer' => 'falls'],
                ],
                'word_bank' => ['cycle', 'atmosphere', 'evaporating', 'falls', 'clouds', 'ocean', 'heating'],
                'difficulty' => 'medium',
            ]
        );

        // Reorder paragraphs
        Question::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Reorder: Scientific Method'],
            [
                'question_type_id' => $reorder->id,
                'paragraphs'       => [
                    ['id' => 1, 'content' => 'Scientists begin by making observations about the natural world and identifying questions they want to answer.'],
                    ['id' => 2, 'content' => 'Based on these observations, they formulate a hypothesis — a testable prediction about what they expect to happen.'],
                    ['id' => 3, 'content' => 'They then design and conduct experiments to test the hypothesis, carefully controlling variables.'],
                    ['id' => 4, 'content' => 'After analyzing the results, scientists draw conclusions and communicate their findings to the scientific community.'],
                ],
                'difficulty' => 'easy',
            ]
        );
    }
}
