<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            
            <!-- User DATA -->
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0)">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold"> EXAMES <?php echo strtoupper($this->clinica()); ?></strong>
                            </span>
                            <span class="text-muted text-xs block"><?php echo $this->getUserTypeName()  ?> <b class="caret"></b></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="admin/users/logout">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    Ceman
                </div>
            </li>

            <!-- Menu Pacientes -->
            <?php if ($this->getUserPermissions('pacientes')): ?>
                <li class="<?php if($this->controller == 'pacientes') print 'active'; ?>">
                    <a href="javascript:void(0)"><i class="fa fa-stethoscope"></i> <span class="nav-label">Pacientes</span></a>
                    <ul class="nav nav-second-level collapse">
                        <?php if ($this->getUserPermissions('pacientes', 'add')): ?>
                            <li><a href="admin/pacientes/add">Cadastrar</a></li>
                        <?php endif; ?>
                        <?php if ($this->getUserPermissions('pacientes', 'index_action')): ?>
                            <li><a href="admin/pacientes">Listar</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Exames -->
            <?php if ($this->getUserPermissions('exames')): ?>
                <li class="<?php if($this->controller == 'exames') print 'active'; ?>">
                    <a href="javascript:void(0)"><i class="fa fa-medkit"></i> <span class="nav-label">Exames</span></a>
                    <ul class="nav nav-second-level collapse">
                        <?php if ($this->getUserPermissions('exames', 'exames_new')): ?>
                            <li><a href="admin/exames/exames_new?clinica=<?=$this->clinica();?>&ativo=ativo">Listar</a></li>
                        <?php endif; ?>
                        <?php if ($this->getUserPermissions('exames', 'exames_new')): ?>
                            <li><a href="admin/exames/exames_new?clinica=<?=$this->clinica();?>&ativo=ativo&backup=false">Exames irregulares</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Médicos -->
            <?php if ($this->clinica() == 'ceman' && $this->getUserPermissions('medicos')): ?>
                <li class="<?php if($this->controller == 'medicos') print 'active'; ?>">
                    <a href="javascript:void(0)"><i class="fa  fa-user-md"></i> <span class="nav-label">Médicos</span></a>
                    <ul class="nav nav-second-level collapse">
                        <?php if ($this->getUserPermissions('medicos', 'add')): ?>
                            <li><a href="admin/medicos/add">Cadastrar</a></li>
                        <?php endif; ?>
                        <?php if ($this->getUserPermissions('medicos', 'index_action')): ?>
                            <li><a href="admin/medicos">Listar</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Menu Usuários -->
            <?php if ($this->clinica() == 'ceman' && $this->getUserPermissions('users')): ?>
                <li class="<?php if($this->controller == 'users') print 'active'; ?>">
                    <a href="javascript:void(0)"><i class="fa fa-user"></i> <span class="nav-label">Usuários</span></a>
                    <ul class="nav nav-second-level collapse">
                        <?php if ($this->getUserPermissions('users', 'add')): ?>
                            <li><a href="admin/users/add">Cadastrar</a></li>
                        <?php endif; ?>
                        <?php if ($this->getUserPermissions('users', 'index_action')): ?>
                            <li><a href="admin/users">Listar</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>