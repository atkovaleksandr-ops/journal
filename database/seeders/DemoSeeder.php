<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = env('DEMO_ADMIN_PASSWORD', 'admin12345');
        $teacherPassword = env('DEMO_TEACHER_PASSWORD', 'password');
        $studentPassword = env('DEMO_STUDENT_PASSWORD', 'student123');

        User::updateOrCreate(
            ['email' => env('DEMO_ADMIN_EMAIL', 'admin@journal.local')],
            [
                'name' => env('DEMO_ADMIN_NAME', 'Администратор'),
                'password' => Hash::make($adminPassword),
                'login_password' => $adminPassword,
                'role' => 'admin',
            ],
        );

        $teacher = User::updateOrCreate(
            ['email' => env('DEMO_TEACHER_EMAIL', 'teacher@journal.local')],
            [
                'name' => env('DEMO_TEACHER_NAME', 'Иван Петрович'),
                'password' => Hash::make($teacherPassword),
                'login_password' => $teacherPassword,
                'role' => 'teacher',
            ],
        );

        foreach ($this->groups() as $groupIndex => $groupData) {
            $group = Group::updateOrCreate(
                ['name' => $groupData['name']],
                ['description' => $groupData['description']],
            );

            $students = $this->seedStudents($group, $groupData, $studentPassword);
            $this->seedSubjects($group, $teacher, $students, $groupData, $groupIndex);
        }
    }

    private function seedStudents(Group $group, array $groupData, string $studentPassword)
    {
        return collect($groupData['students'])->map(function (array $name, int $index) use ($group, $groupData, $studentPassword) {
            $studentNumber = strtoupper($groupData['email_prefix']) . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            $email = $groupData['email_prefix'] . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT) . '@journal.local';
            $fullName = $name['last_name'] . ' ' . $name['first_name'];

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'password' => Hash::make($studentPassword),
                    'login_password' => $studentPassword,
                    'role' => 'student',
                ],
            );

            return Student::updateOrCreate(
                ['student_number' => $studentNumber],
                [
                    'first_name' => $name['first_name'],
                    'last_name' => $name['last_name'],
                    'email' => $email,
                    'login_password' => $studentPassword,
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                ],
            );
        });
    }

    private function seedSubjects(Group $group, User $teacher, $students, array $groupData, int $groupIndex): void
    {
        $baseDate = Carbon::today()->subWeeks(10)->addDays($groupIndex * 3);

        foreach ($groupData['subjects'] as $subjectIndex => $subjectData) {
            $subject = Subject::updateOrCreate(
                [
                    'name' => $subjectData['name'],
                    'group_id' => $group->id,
                    'teacher_id' => $teacher->id,
                ],
                ['description' => $subjectData['description']],
            );

            foreach ($subjectData['topics'] as $lessonIndex => $topic) {
                $lesson = Lesson::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'group_id' => $group->id,
                        'teacher_id' => $teacher->id,
                        'date' => $baseDate->copy()->addDays($lessonIndex * 7 + $subjectIndex)->toDateString(),
                        'topic' => $topic,
                    ],
                    ['description' => 'Практическое занятие по теме "' . $topic . '".'],
                );

                foreach ($students->values() as $studentIndex => $student) {
                    $isAbsent = ($studentIndex + $lessonIndex + $subjectIndex) % 7 === 0;

                    Attendance::updateOrCreate(
                        [
                            'lesson_id' => $lesson->id,
                            'student_id' => $student->id,
                        ],
                        [
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacher->id,
                            'date' => $lesson->date,
                            'status' => $isAbsent ? Attendance::STATUS_ABSENT : Attendance::STATUS_PRESENT,
                            'note' => $isAbsent ? 'Отсутствие отмечено преподавателем.' : null,
                        ],
                    );
                }
            }
        }
    }

    private function groups(): array
    {
        return [
            [
                'name' => 'ВТ-23',
                'description' => 'Вычислительные технологии, 2 курс, 3 группа',
                'email_prefix' => 'vt23',
                'students' => $this->names(0, 10),
                'subjects' => [
                    $this->subject('Архитектура компьютера', 'Устройство ПК, память, процессоры и периферия.', [
                        'Компоненты системного блока',
                        'Память и накопители',
                        'Процессор и шины данных',
                        'Настройка BIOS и UEFI',
                        'Сборка учебной станции',
                        'Диагностика неисправностей',
                        'Периферийные устройства',
                        'Итоговая практическая работа',
                    ]),
                    $this->subject('Операционные системы', 'Работа с Windows, Linux и пользовательскими правами.', [
                        'Файловые системы',
                        'Пользователи и права доступа',
                        'Командная строка',
                        'Процессы и службы',
                        'Установка драйверов',
                        'Резервное копирование',
                        'Сетевые настройки',
                        'Контрольная настройка среды',
                    ]),
                    $this->subject('Электроника', 'Базовые схемы, измерения и техника безопасности.', [
                        'Электрические величины',
                        'Работа с мультиметром',
                        'Резисторы и конденсаторы',
                        'Диоды и транзисторы',
                        'Макетная плата',
                        'Простая схема питания',
                        'Поиск ошибки в цепи',
                        'Защита лабораторной работы',
                    ]),
                    $this->subject('Компьютерные сети', 'Локальные сети, адресация и базовая диагностика.', [
                        'Модель OSI',
                        'IP-адресация',
                        'Маршрутизация',
                        'DNS и DHCP',
                        'Коммутаторы',
                        'Беспроводные сети',
                        'Диагностика ping и tracert',
                        'Схема сети кабинета',
                    ]),
                ],
            ],
            [
                'name' => 'ДИ-21',
                'description' => 'Дизайн интерфейсов, 2 курс, 1 группа',
                'email_prefix' => 'di21',
                'students' => $this->names(10, 9),
                'subjects' => [
                    $this->subject('UX-проектирование', 'Исследование пользователей и сценарии взаимодействия.', [
                        'Портрет пользователя',
                        'Карта пути клиента',
                        'Информационная архитектура',
                        'Прототипирование формы',
                        'Юзабилити-тестирование',
                        'Сценарии ошибок',
                        'Доступность интерфейса',
                        'Защита прототипа',
                    ]),
                    $this->subject('Графический дизайн', 'Композиция, типографика и визуальная система проекта.', [
                        'Композиционная сетка',
                        'Работа с цветом',
                        'Типографика',
                        'Иконки и пиктограммы',
                        'Дизайн карточек',
                        'Подготовка макета',
                        'Адаптация под мобильный экран',
                        'Финальный экран сервиса',
                    ]),
                    $this->subject('Figma-практикум', 'Компоненты, варианты, автолейаут и подготовка к передаче.', [
                        'Автолейаут',
                        'Компоненты',
                        'Варианты компонентов',
                        'Стили текста',
                        'Стили цвета',
                        'Интерактивный прототип',
                        'Передача разработчику',
                        'Дизайн-система страницы',
                    ]),
                ],
            ],
            [
                'name' => 'ИС-21',
                'description' => 'Информационные системы, 2 курс, 1 группа',
                'email_prefix' => 'is21',
                'students' => $this->names(19, 12),
                'subjects' => [
                    $this->subject('Информационные системы', 'Проектирование, роли пользователей и бизнес-процессы.', [
                        'Назначение информационных систем',
                        'Роли и права доступа',
                        'Бизнес-процессы',
                        'Диаграмма вариантов использования',
                        'Требования к системе',
                        'Жизненный цикл проекта',
                        'Документация пользователя',
                        'Итоговая спецификация',
                    ]),
                    $this->subject('Базы данных', 'Таблицы, связи, SQL-запросы и нормализация.', [
                        'Таблицы и поля',
                        'Первичные ключи',
                        'Связи один ко многим',
                        'SELECT-запросы',
                        'Фильтрация данных',
                        'JOIN-запросы',
                        'Нормализация',
                        'Мини-проект базы данных',
                    ]),
                    $this->subject('Web-разработка', 'Маршруты, шаблоны, формы и простая авторизация.', [
                        'HTML-структура',
                        'CSS-сетка',
                        'Формы и валидация',
                        'Маршруты приложения',
                        'Контроллеры',
                        'Шаблоны Blade',
                        'Работа с данными',
                        'Публикация проекта',
                    ]),
                    $this->subject('Анализ данных', 'Сбор, очистка, сводные таблицы и простые графики.', [
                        'Типы данных',
                        'Очистка таблицы',
                        'Формулы',
                        'Сводная таблица',
                        'Диаграммы',
                        'Поиск аномалий',
                        'Интерпретация результата',
                        'Отчет по набору данных',
                    ]),
                ],
            ],
            [
                'name' => 'ПО-12',
                'description' => 'Программное обеспечение, 1 курс, 2 группа',
                'email_prefix' => 'po12',
                'students' => $this->names(31, 8),
                'subjects' => [
                    $this->subject('Алгоритмизация', 'Блок-схемы, условия, циклы и базовые алгоритмы.', [
                        'Понятие алгоритма',
                        'Линейные алгоритмы',
                        'Условия',
                        'Циклы',
                        'Массивы',
                        'Поиск максимума',
                        'Сортировка простым обменом',
                        'Практическая задача',
                    ]),
                    $this->subject('Основы программирования', 'Переменные, функции и отладка простых программ.', [
                        'Среда разработки',
                        'Переменные',
                        'Операторы',
                        'Функции',
                        'Строки',
                        'Списки',
                        'Отладчик',
                        'Мини-программа',
                    ]),
                    $this->subject('Основы баз данных', 'Структура таблиц и первые SQL-запросы.', [
                        'Что такое база данных',
                        'Создание таблицы',
                        'Типы полей',
                        'Добавление записей',
                        'Выборка данных',
                        'Обновление записей',
                        'Удаление записей',
                        'Итоговая таблица',
                    ]),
                ],
            ],
            [
                'name' => 'ПО-22',
                'description' => 'Программное обеспечение, 2 курс, 2 группа',
                'email_prefix' => 'po22',
                'students' => $this->names(39, 10),
                'subjects' => [
                    $this->subject('Программирование', 'ООП, коллекции, обработка ошибок и практика кода.', [
                        'Классы и объекты',
                        'Инкапсуляция',
                        'Наследование',
                        'Полиморфизм',
                        'Коллекции',
                        'Исключения',
                        'Работа с файлами',
                        'Модульная задача',
                    ]),
                    $this->subject('Web-технологии', 'HTTP, формы, адаптивная верстка и серверная логика.', [
                        'HTTP-запросы',
                        'HTML-формы',
                        'CSS Grid',
                        'Адаптивная верстка',
                        'Работа с API',
                        'Сессии',
                        'Загрузка файлов',
                        'Публикация страницы',
                    ]),
                    $this->subject('Тестирование ПО', 'Проверки, тест-кейсы, баг-репорты и регрессия.', [
                        'Виды тестирования',
                        'Тест-кейс',
                        'Чек-лист',
                        'Баг-репорт',
                        'Граничные значения',
                        'Регрессия',
                        'Автотесты',
                        'Итоговый отчет',
                    ]),
                ],
            ],
        ];
    }

    private function subject(string $name, string $description, array $topics): array
    {
        return compact('name', 'description', 'topics');
    }

    private function names(int $offset, int $count): array
    {
        return array_slice([
            ['first_name' => 'Олег', 'last_name' => 'Ахметов'],
            ['first_name' => 'Карина', 'last_name' => 'Васильева'],
            ['first_name' => 'Кирилл', 'last_name' => 'Егоров'],
            ['first_name' => 'София', 'last_name' => 'Лебедева'],
            ['first_name' => 'Артем', 'last_name' => 'Новиков'],
            ['first_name' => 'Сабина', 'last_name' => 'Тлеубергенова'],
            ['first_name' => 'Жанель', 'last_name' => 'Токтарова'],
            ['first_name' => 'Михаил', 'last_name' => 'Федоров'],
            ['first_name' => 'Алина', 'last_name' => 'Петрова'],
            ['first_name' => 'Дамир', 'last_name' => 'Сейдахметов'],
            ['first_name' => 'Мария', 'last_name' => 'Ким'],
            ['first_name' => 'Илья', 'last_name' => 'Орлов'],
            ['first_name' => 'Аружан', 'last_name' => 'Смагулова'],
            ['first_name' => 'Никита', 'last_name' => 'Зайцев'],
            ['first_name' => 'Диана', 'last_name' => 'Кузнецова'],
            ['first_name' => 'Руслан', 'last_name' => 'Тимофеев'],
            ['first_name' => 'Виктория', 'last_name' => 'Громова'],
            ['first_name' => 'Ерасыл', 'last_name' => 'Абилов'],
            ['first_name' => 'Полина', 'last_name' => 'Соколова'],
            ['first_name' => 'Айдана', 'last_name' => 'Нурланова'],
            ['first_name' => 'Алексей', 'last_name' => 'Морозов'],
            ['first_name' => 'Мадина', 'last_name' => 'Омарова'],
            ['first_name' => 'Тимур', 'last_name' => 'Рахимов'],
            ['first_name' => 'Екатерина', 'last_name' => 'Ильина'],
            ['first_name' => 'Данил', 'last_name' => 'Белов'],
            ['first_name' => 'Амина', 'last_name' => 'Калиева'],
            ['first_name' => 'Сергей', 'last_name' => 'Николаев'],
            ['first_name' => 'Лаура', 'last_name' => 'Абдрахманова'],
            ['first_name' => 'Роман', 'last_name' => 'Павлов'],
            ['first_name' => 'Милана', 'last_name' => 'Турсунова'],
            ['first_name' => 'Арман', 'last_name' => 'Жаксылыков'],
            ['first_name' => 'Елена', 'last_name' => 'Сидорова'],
            ['first_name' => 'Бекзат', 'last_name' => 'Муратов'],
            ['first_name' => 'Юлия', 'last_name' => 'Андреева'],
            ['first_name' => 'Игорь', 'last_name' => 'Смирнов'],
            ['first_name' => 'Назым', 'last_name' => 'Исаева'],
            ['first_name' => 'Павел', 'last_name' => 'Волков'],
            ['first_name' => 'Асия', 'last_name' => 'Байжанова'],
            ['first_name' => 'Константин', 'last_name' => 'Макаров'],
            ['first_name' => 'Гульназ', 'last_name' => 'Сапарова'],
            ['first_name' => 'Денис', 'last_name' => 'Попов'],
            ['first_name' => 'Меруерт', 'last_name' => 'Ермекова'],
            ['first_name' => 'Степан', 'last_name' => 'Кравцов'],
            ['first_name' => 'Лилия', 'last_name' => 'Зарипова'],
            ['first_name' => 'Владислав', 'last_name' => 'Семенов'],
            ['first_name' => 'Камила', 'last_name' => 'Тажибаева'],
            ['first_name' => 'Максим', 'last_name' => 'Лазарев'],
            ['first_name' => 'Индира', 'last_name' => 'Сулейменова'],
            ['first_name' => 'Антон', 'last_name' => 'Гаврилов'],
        ], $offset, $count);
    }
}
