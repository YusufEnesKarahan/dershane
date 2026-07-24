<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Repositories\Interfaces\ParentRepositoryInterface;
use App\Core\Repositories\Interfaces\TeacherPortalRepositoryInterface;
use App\Domain\Parent\Services\ParentPortalService;
use App\Domain\Teacher\Services\TeacherPortalService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PortalScopeAuthorizationTest extends TestCase
{
    public function test_parent_cannot_load_an_unlinked_student_dashboard(): void
    {
        $repository = $this->createMock(ParentRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('isStudentLinked')
            ->with(10, 99)
            ->willReturn(false);

        $service = new ParentPortalService($repository);

        try {
            $service->getDashboardData(10, 99);
            $this->fail('An unlinked student must not be rendered for a parent.');
        } catch (HttpException $exception) {
            $this->assertSame(404, $exception->getStatusCode());
        }
    }

    public function test_parent_scope_accepts_a_linked_student(): void
    {
        $repository = $this->createMock(ParentRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('isStudentLinked')
            ->with(10, 20)
            ->willReturn(true);

        $service = new ParentPortalService($repository);

        $this->assertTrue($service->canAccessStudent(10, 20));
    }

    public function test_teacher_cannot_manage_an_unassigned_class_course_pair(): void
    {
        $repository = $this->createMock(TeacherPortalRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('hasAssignment')
            ->with(10, 99, 5)
            ->willReturn(false);

        $service = new TeacherPortalService($repository);

        $this->assertFalse($service->canManageClassCourse(10, 99, 5));
    }

    public function test_teacher_can_manage_an_assigned_class_course_pair(): void
    {
        $repository = $this->createMock(TeacherPortalRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('hasAssignment')
            ->with(10, 20, 5)
            ->willReturn(true);

        $service = new TeacherPortalService($repository);

        $this->assertTrue($service->canManageClassCourse(10, 20, 5));
    }
}
