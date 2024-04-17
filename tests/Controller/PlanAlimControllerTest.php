<?php

namespace App\Test\Controller;

use App\Entity\PlanAlim;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlanAlimControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/plan/alim/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(PlanAlim::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PlanAlim index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'plan_alim[idNut]' => 'Testing',
            'plan_alim[idUser]' => 'Testing',
            'plan_alim[idRegime]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new PlanAlim();
        $fixture->setIdNut('My Title');
        $fixture->setIdUser('My Title');
        $fixture->setIdRegime('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PlanAlim');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new PlanAlim();
        $fixture->setIdNut('Value');
        $fixture->setIdUser('Value');
        $fixture->setIdRegime('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'plan_alim[idNut]' => 'Something New',
            'plan_alim[idUser]' => 'Something New',
            'plan_alim[idRegime]' => 'Something New',
        ]);

        self::assertResponseRedirects('/plan/alim/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getIdNut());
        self::assertSame('Something New', $fixture[0]->getIdUser());
        self::assertSame('Something New', $fixture[0]->getIdRegime());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new PlanAlim();
        $fixture->setIdNut('Value');
        $fixture->setIdUser('Value');
        $fixture->setIdRegime('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/plan/alim/');
        self::assertSame(0, $this->repository->count([]));
    }
}
