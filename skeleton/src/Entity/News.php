<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Provider\ApiProvider;
use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(/*openapiContext: ['tags' => ['Пользователь']]*/
    operations: [
        new GetCollection(
            openapi: new Operation(
                tags:[
                    'Новости'
                ],
                summary: 'Получить Новости',
                description: 'Получить весь список Новостей',
                parameters: [
                    new Parameter(
                        name: 'page',
                        in: 'query',
                        schema: [
                            'type' => 'integer',
                            'default' => 1,
                        ]
                    )
                ]
            )
        ),
        new GetCollection(
            uriTemplate: '/news_tape',
            openapi: new Operation(
                tags:[
                    'Новости'
                ],
                summary: 'Получить Новости',
                description: 'Получить список новостей пользователей',
                parameters: [
                    new Parameter(
                        name: 'page',
                        in: 'query',
                        schema: [
                            'type' => 'integer',
                            'default' => 1,
                        ]
                    )
                ],
            ),
            provider: ApiProvider::class,
        ),
        new Get(
            openapi: new Operation(
                tags:[
                    'Новости'
                ],
                summary: 'Получить Новости',
                description: 'Получить Новость',
                parameters: [
                    new Parameter(
                        name: 'page',
                        in: 'query',
                        schema: [
                            'type' => 'integer',
                            'default' => 1,
                        ]
                    )
                ]
            )
        ),
        new Post(
            openapi: new Operation(
                tags:[
                    'Новости'
                ],
                summary: 'Создать Новость',
                description: 'Создать Новость',
                parameters: [
                    new Parameter(
                        name: 'page',
                        in: 'query',
                        schema: [
                            'type' => 'integer',
                            'default' => 1,
                        ]
                    )
                ]
            )
        ),
        new Put(
            openapi: new Operation(
                tags:[
                    'Новости'
                ],
                summary: 'Изменить Новость',
                description: 'Изменить Новость',
                parameters: [
                    new Parameter(
                        name: 'page',
                        in: 'query',
                        schema: [
                            'type' => 'integer',
                            'default' => 1,
                        ]
                    )
                ]
            )
        ),
        new Delete(
            openapi: new Operation(
                tags:[
                    'Новости'
                ],
                summary: 'Удалить Новость',
                description: 'Удалить Новость',
                parameters: [
                    new Parameter(
                        name: 'page',
                        in: 'query',
                        schema: [
                            'type' => 'integer',
                            'default' => 1,
                        ]
                    )
                ]
            )
        ),
        new Patch(
            openapi: new Operation(
                tags:[
                    'Новости'
                ],
                summary: 'Узнать путь Новости',
                description: 'Узнать путь Новости',
                parameters: [
                    new Parameter(
                        name: 'page',
                        in: 'query',
                        schema: [
                            'type' => 'integer',
                            'default' => 1,
                        ]
                    )
                ]
            )
        )
    ]
)]
#[ORM\Entity(repositoryClass: NewsRepository::class)]
class News
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255)]
    #[Assert\NotNull]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'news')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Content $content = null;

    /**
     * @var Collection<int, ContentNews>
     */
    #[ORM\OneToMany(targetEntity: ContentNews::class, mappedBy: 'news', orphanRemoval: true)]
    private Collection $contentNews;

    public function __construct()
    {
        $this->contentNews = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, ContentNews>
     */
    public function getContentNews(): Collection
    {
        return $this->contentNews;
    }

    public function addContentNews(ContentNews $contentNews): static
    {
        if (!$this->contentNews->contains($contentNews)) {
            $this->contentNews->add($contentNews);
            $contentNews->setNews($this);
        }

        return $this;
    }

    public function removeContentNews(ContentNews $contentNews): static
    {
        if ($this->contentNews->removeElement($contentNews)) {
            // set the owning side to null (unless already changed)
            if ($contentNews->getNews() === $this) {
                $contentNews->setNews(null);
            }
        }

        return $this;
    }
}
