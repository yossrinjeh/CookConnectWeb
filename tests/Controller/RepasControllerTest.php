<?php

namespace App\Test\Controller;

use App\Entity\Repas;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RepasControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/repas/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Repas::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Repa index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'repa[idRecette]' => 'Testing',
            'repa[idUser]' => 'Testing',
            'repa[nom]' => 'Testing',
            'repa[type]' => 'Testing',
            'repa[tags]' => 'Testing',
            'repa[email]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Repas();
        $fixture->setIdRecette('My Title');
        $fixture->setIdUser('My Title');
        $fixture->setNom('My Title');
        $fixture->setType('My Title');
        $fixture->setTags('My Title');
        $fixture->setEmail('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Repa');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Repas();
        $fixture->setIdRecette('Value');
        $fixture->setIdUser('Value');
        $fixture->setNom('Value');
        $fixture->setType('Value');
        $fixture->setTags('Value');
        $fixture->setEmail('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'repa[idRecette]' => 'Something New',
            'repa[idUser]' => 'Something New',
            'repa[nom]' => 'Something New',
            'repa[type]' => 'Something New',
            'repa[tags]' => 'Something New',
            'repa[email]' => 'Something New',
        ]);

        self::assertResponseRedirects('/repas/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getIdRecette());
        self::assertSame('Something New', $fixture[0]->getIdUser());
        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getTags());
        self::assertSame('Something New', $fixture[0]->getEmail());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Repas();
        $fixture->setIdRecette('Value');
        $fixture->setIdUser('Value');
        $fixture->setNom('Value');
        $fixture->setType('Value');
        $fixture->setTags('Value');
        $fixture->setEmail('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/repas/');
        self::assertSame(0, $this->repository->count([]));
    }
}
