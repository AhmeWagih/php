<?php

class User
{
  private mysqli $connection;
  private int $id = 0;
  private string $firstName = '';
  private string $lastName = '';
  private string $department = '';
  private string $country = '';
  private string $gender = '';
  private string $address = '';
  private string $email = '';
  private string $password = '';
  private array $skills = [];
  private string $image = '';

  public function __construct(mysqli $connection)
  {
    $this->connection = $connection;
  }

  


  public function getId(): int
  {
    return $this->id;
  }

  public function setId(int $id): self
  {
    $this->id = $id;

    return $this;
  }

  public function getFirstName(): string
  {
    return $this->firstName;
  }

  public function setFirstName(string $firstName): self
  {
    $this->firstName = trim($firstName);

    return $this;
  }

  public function getLastName(): string
  {
    return $this->lastName;
  }

  public function setLastName(string $lastName): self
  {
    $this->lastName = trim($lastName);

    return $this;
  }

  public function getDepartment(): string
  {
    return $this->department;
  }

  public function setDepartment(string $department): self
  {
    $this->department = trim($department);

    return $this;
  }

  public function getCountry(): string
  {
    return $this->country;
  }

  public function setCountry(string $country): self
  {
    $this->country = trim($country);

    return $this;
  }

  public function getGender(): string
  {
    return $this->gender;
  }

  public function setGender(string $gender): self
  {
    $this->gender = trim($gender);

    return $this;
  }

  public function getAddress(): string
  {
    return $this->address;
  }

  public function setAddress(string $address): self
  {
    $this->address = trim($address);

    return $this;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = trim($email);

    return $this;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  public function getSkills(): array
  {
    return $this->skills;
  }

  public function setSkills(array $skills): self
  {
    $normalizedSkills = [];
    foreach ($skills as $skill) {
      $trimmedSkill = trim((string) $skill);
      if ($trimmedSkill !== '') {
        $normalizedSkills[] = $trimmedSkill;
      }
    }

    $this->skills = array_values($normalizedSkills);

    return $this;
  }

  public function getImage(): string
  {
    return $this->image;
  }

  public function setImage(string $image): self
  {
    $this->image = trim($image);

    return $this;
  }

  public function getFullName(): string
  {
    return trim($this->firstName . ' ' . $this->lastName);
  }

  public function hydrateFromArray(array $data): self
  {
    $this->setId((int) ($data['id'] ?? 0));
    $this->setFirstName((string) ($data['first_name'] ?? $data['firstName'] ?? ''));
    $this->setLastName((string) ($data['last_name'] ?? $data['lastName'] ?? ''));
    $this->setDepartment((string) ($data['department'] ?? ''));
    $this->setCountry((string) ($data['country'] ?? ''));
    $this->setGender((string) ($data['gender'] ?? ''));
    $this->setAddress((string) ($data['address'] ?? ''));
    $this->setEmail((string) ($data['email'] ?? ''));
    $this->setPassword((string) ($data['password'] ?? ''));

    $skillsValue = $data['skills'] ?? [];
    if (is_string($skillsValue)) {
      $decoded = json_decode($skillsValue, true);
      if (is_array($decoded)) {
        $this->setSkills($decoded);
      } else {
        $this->setSkills([]);
      }
    } elseif (is_array($skillsValue)) {
      $this->setSkills($skillsValue);
    } else {
      $this->setSkills([]);
    }

    $this->setImage((string) ($data['image'] ?? ''));

    return $this;
  }

  public function validate(bool $requirePassword = true): array
  {
    $errors = [];

    if ($this->firstName === '') {
      $errors[] = 'First name is required.';
    } elseif (strlen($this->firstName) < 3) {
      $errors[] = 'First Name Must be more than 3 letters';
    }

    if ($this->lastName === '') {
      $errors[] = 'Last name is required.';
    } elseif (strlen($this->lastName) < 3) {
      $errors[] = 'Last Name must be more than 3 letters.';
    }

    if ($this->email === '') {
      $errors[] = 'Email is required.';
    } else {
      if (!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$/', $this->email)) {
        $errors[] = 'Email is invalid (regex validation failed).';
      }

      if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is invalid (filter_var validation failed).';
      }
    }

    if ($requirePassword || $this->password !== '') {
      if ($this->password === '') {
        $errors[] = 'Password is required.';
      } else {
        if (strlen($this->password) !== 8) {
          $errors[] = 'Password must be exactly 8 characters.';
        }

        if (!preg_match('/^[A-Za-z0-9_]+$/', $this->password)) {
          $errors[] = 'Password: No special characters allowed. Only underscore (_) is permitted.';
        }

        if (preg_match('/[A-Z]/', $this->password)) {
          $errors[] = 'Password: No capital characters allowed.';
        }
      }
    }

    return $errors;
  }

  public function findAll(): array
  {
    $users = [];
    $result = $this->connection->query(
      'SELECT id, first_name, last_name, department, country, gender, email, skills FROM users ORDER BY id DESC'
    );

    if ($result) {
      while ($row = $result->fetch_assoc()) {
        $users[] = $row;
      }
      $result->free();
    }

    return $users;
  }

  public function findById(int $id): ?array
  {
    if ($id <= 0) {
      return null;
    }

    $stmt = $this->connection->prepare(
      'SELECT id, first_name, last_name, department, country, gender, address, email, password, skills, image
            FROM users
            WHERE id = ?
            LIMIT 1'
    );

    if (!$stmt) {
      return null;
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $user ?: null;
  }

  public function findByEmail(string $email): ?array
  {
    $trimmedEmail = trim($email);
    if ($trimmedEmail === '') {
      return null;
    }

    $stmt = $this->connection->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');

    if (!$stmt) {
      return null;
    }

    $stmt->bind_param('s', $trimmedEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = ($result && $result->num_rows === 1) ? $result->fetch_assoc() : null;
    $stmt->close();

    return $user ?: null;
  }

  public function create(): bool
  {
    $skillsJson = json_encode($this->skills);
    if ($skillsJson === false) {
      $skillsJson = '[]';
    }

    $stmt = $this->connection->prepare(
      'INSERT INTO users (first_name, last_name, email, password, country, address, gender, department, skills)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    if (!$stmt) {
      return false;
    }

    $stmt->bind_param(
      'sssssssss',
      $this->firstName,
      $this->lastName,
      $this->email,
      $this->password,
      $this->country,
      $this->address,
      $this->gender,
      $this->department,
      $skillsJson
    );

    $isCreated = $stmt->execute();
    if ($isCreated) {
      $this->id = (int) $this->connection->insert_id;
    }
    $stmt->close();

    return $isCreated;
  }

  public function updateById(int $id): bool
  {
    if ($id <= 0) {
      return false;
    }

    $skillsJson = json_encode($this->skills);
    if ($skillsJson === false) {
      $skillsJson = '[]';
    }

    $stmt = $this->connection->prepare(
      'UPDATE users
            SET first_name = ?, last_name = ?, email = ?, password = ?, country = ?, address = ?, gender = ?, department = ?, skills = ?
            WHERE id = ?
            LIMIT 1'
    );

    if (!$stmt) {
      return false;
    }

    $stmt->bind_param(
      'sssssssssi',
      $this->firstName,
      $this->lastName,
      $this->email,
      $this->password,
      $this->country,
      $this->address,
      $this->gender,
      $this->department,
      $skillsJson,
      $id
    );

    $isUpdated = $stmt->execute();
    $stmt->close();

    return $isUpdated;
  }

  public function deleteById(int $id): bool
  {
    if ($id <= 0) {
      return false;
    }

    $stmt = $this->connection->prepare('DELETE FROM users WHERE id = ? LIMIT 1');

    if (!$stmt) {
      return false;
    }

    $stmt->bind_param('i', $id);
    $isDeleted = $stmt->execute();
    $stmt->close();

    return $isDeleted;
  }
}
